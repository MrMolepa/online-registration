<?php

namespace App\Services;

use App\Models\SubjectGroup;
use App\Models\SubjectGroupRule;
use App\Models\Center;


class SubjectValidator
{
    protected $errors = [];
    protected $warnings = [];

    /**
     * Validate subject selection against rules
     * 
     * @param array $subjectCodes Array of selected subject codes
     * @param int $levelId Level ID
     * @param int $type Registration type (1=Full, 2=Partial, 3=Private)
     * @param string|null $centerNo Optional center number for center-specific validation
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array]
     */
    public function validate(array $subjectCodes, int $levelId, int $type, ?string $centerNo = null)
    {
        $this->errors = [];
        $this->warnings = [];

        // Get applicable rule
        $rule = SubjectGroupRule::active()
            ->forLevel($levelId)
            ->forType($type)
            ->first();

        // Get all groups for this level
        $groups = SubjectGroup::active()
            ->forLevel($levelId)
            ->with('subjects')
            ->get();

        // Determine which groups the selected subjects belong to
        $selectedGroups = $this->identifySelectedGroups($subjectCodes, $groups);

        // Validate min/max subjects
        $this->validateSubjectCount($subjectCodes, $rule);

        // Validate required groups
        $this->validateRequiredGroups($selectedGroups, $rule, $groups);

        // Validate forbidden groups
        $this->validateForbiddenGroups($selectedGroups, $rule, $groups);

        // Validate group constraints
        $this->validateGroupConstraints($selectedGroups, $rule, $groups);

        // Validate incompatible subject pairs
        $this->validateIncompatiblePairs($subjectCodes, $rule);

        // Center-specific validation
        if ($centerNo) {
            $this->validateCenterSubjects($subjectCodes, $centerNo);
        }

        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'selected_groups' => $selectedGroups,
        ];
    }

    /**
     * Identify which groups contain the selected subjects
     */
    protected function identifySelectedGroups(array $subjectCodes, $groups)
    {
        $selectedGroups = [];

        $normalizedCodes = array_map(fn($code) => str_pad((string) $code, 4, '0', STR_PAD_LEFT), $subjectCodes);

        foreach ($groups as $group) {
            $groupSubjects = $group->subjects->pluck('subject_code')->toArray();

            $intersection = array_intersect($normalizedCodes, $groupSubjects);

            if (!empty($intersection)) {
                $selectedGroups[(string) $group->group_code] = [
                    'group_id' => $group->id,
                    'group_name' => $group->group_name,
                    'subject_count' => count($intersection),
                    'subjects' => $intersection,
                ];
            }
        }

        return $selectedGroups;
    }

    /**
     * Validate subject count constraints
     */
    protected function validateSubjectCount(array $subjectCodes, SubjectGroupRule $rule)
    {
        $count = count($subjectCodes);
        $minSubjects = $rule->min_subjects;
        $maxSubjects = $rule->max_subjects;

        if ($minSubjects && $count < $minSubjects) {
            $this->errors[] = "Minimum of {$minSubjects} subjects required. You selected {$count}.";
        }

        if ($maxSubjects && $count > $maxSubjects) {
            $this->errors[] = "Maximum of {$maxSubjects} subjects allowed. You selected {$count}.";
        }
    }

    /**
     * Validate required groups
     */
    protected function validateRequiredGroups(array $selectedGroups, SubjectGroupRule $rule, $groups)
    {
        $requiredGroups = $rule->required_groups;

        foreach ($requiredGroups as $requiredGroupConfig) {
            $groupCode = (string) (is_array($requiredGroupConfig)
                ? $requiredGroupConfig['group_code']
                : $requiredGroupConfig);

            $minCount = is_array($requiredGroupConfig) ? ($requiredGroupConfig['min_count'] ?? 1) : 1;
            $maxCount = is_array($requiredGroupConfig) ? ($requiredGroupConfig['max_count'] ?? null) : null;

            $group = $groups->firstWhere('group_code', $groupCode);

            if (!$group) {
                \Log::warning("SubjectGroupRule references group_code '{$groupCode}' which does not exist in SubjectGroups for level.");
                continue;
            }

            if (!isset($selectedGroups[$groupCode])) {
                $this->errors[] = "You must select at least {$minCount} subject(s) from: {$group->group_name}";
                continue;
            }

            $selectedCount = $selectedGroups[$groupCode]['subject_count'];

            if ($selectedCount < $minCount) {
                $this->errors[] = "You must select at least {$minCount} subject(s) from: {$group->group_name}. You selected {$selectedCount}.";
            }

            if ($maxCount && $selectedCount > $maxCount) {
                $this->errors[] = "You can select maximum {$maxCount} subject(s) from: {$group->group_name}. You selected {$selectedCount}.";
            }
        }
    }
    /**
     * Validate forbidden groups
     */
    protected function validateForbiddenGroups(array $selectedGroups, SubjectGroupRule $rule, $groups)
    {
        $forbiddenGroups = $rule->forbidden_groups;

        foreach ($forbiddenGroups as $forbiddenGroupCode) {
            $forbiddenGroupCode = (string) $forbiddenGroupCode;
            if (isset($selectedGroups[$forbiddenGroupCode])) {
                $group = $groups->firstWhere('group_code', $forbiddenGroupCode);
                $groupName = $group ? $group->group_name : $forbiddenGroupCode;
                $this->errors[] = "Subjects from '{$groupName}' are not allowed for this registration type.";
            }
        }
    }

    /**
     * Validate group constraints (complex rules)
     */
    protected function validateGroupConstraints(array $selectedGroups, SubjectGroupRule $rule, $groups)
    {
        $constraints = $rule->group_constraints;

        foreach ($constraints as $constraint) {
            $type = $constraint['type'] ?? null;

            switch ($type) {
                case 'at_least_one_from_multiple':
                    $this->validateAtLeastOneFromMultiple($selectedGroups, $constraint, $groups);
                    break;

                case 'mutually_exclusive':
                    $this->validateMutuallyExclusive($selectedGroups, $constraint, $groups);
                    break;

                case 'conditional_required':
                    $this->validateConditionalRequired($selectedGroups, $constraint, $groups);
                    break;

                case 'min_total_from_groups':
                    $this->validateMinTotalFromGroups($selectedGroups, $constraint, $groups);
                    break;
            }
        }
    }

    /**
     * Validate: Must select at least one subject from specified groups
     */
    protected function validateAtLeastOneFromMultiple(array $selectedGroups, array $constraint, $groups)
    {
        $groupCodes = $constraint['groups'] ?? [];
        $hasSelection = false;

        foreach ($groupCodes as $groupCode) {
            $groupCode = (string) $groupCode;
            if (isset($selectedGroups[$groupCode]) && $selectedGroups[$groupCode]['subject_count'] > 0) {
                $hasSelection = true;
                break;
            }
        }

        if (!$hasSelection) {
            $groupNames = $groups->whereIn('group_code', $groupCodes)->pluck('group_name')->toArray();
            $message = $constraint['message'] ?? "You must select at least one subject from: " . implode(', ', $groupNames);
            $this->errors[] = $message;
        }
    }

    /**
     * Validate: Groups are mutually exclusive
     */
    protected function validateMutuallyExclusive(array $selectedGroups, array $constraint, $groups)
    {
        $groupCodes = $constraint['groups'] ?? [];
        $selectedCount = 0;

        foreach ($groupCodes as $groupCode) {
            $groupCode = (string) $groupCode;
            if (isset($selectedGroups[$groupCode])) {
                $selectedCount++;
            }
        }

        if ($selectedCount > 1) {
            $groupNames = $groups->whereIn('group_code', $groupCodes)->pluck('group_name')->toArray();
            $message = $constraint['message'] ?? "You can only select subjects from ONE of: " . implode(', ', $groupNames);
            $this->errors[] = $message;
        }
    }

    /**
     * Validate: If group A is selected, group B is required
     */
    protected function validateConditionalRequired(array $selectedGroups, array $constraint, $groups)
    {
        $ifGroup = (string) ($constraint['if_group'] ?? null);
        $thenGroup = (string) ($constraint['then_group'] ?? null);
        $minCount = $constraint['min_count'] ?? 1;

        if (!$ifGroup || !$thenGroup) {
            return;
        }

        if (isset($selectedGroups[$ifGroup])) {
            if (!isset($selectedGroups[$thenGroup]) || $selectedGroups[$thenGroup]['subject_count'] < $minCount) {
                $thenGroupObj = $groups->firstWhere('group_code', $thenGroup);
                $thenGroupName = $thenGroupObj ? $thenGroupObj->group_name : $thenGroup;
                $message = $constraint['message'] ?? "Since you selected from '{$ifGroup}', you must also select at least {$minCount} subject(s) from: {$thenGroupName}";
                $this->errors[] = $message;
            }
        }
    }

    /**
     * Validate: Minimum total subjects from specified groups
     */
    protected function validateMinTotalFromGroups(array $selectedGroups, array $constraint, $groups)
    {
        $groupCodes = $constraint['groups'] ?? [];
        $minTotal = $constraint['min_total'] ?? 0;
        $totalCount = 0;

        foreach ($groupCodes as $groupCode) {
            $groupCode = (string) $groupCode;
            if (isset($selectedGroups[$groupCode])) {
                $totalCount += $selectedGroups[$groupCode]['subject_count'];
            }
        }

        if ($totalCount < $minTotal) {
            $groupNames = $groups->whereIn('group_code', $groupCodes)->pluck('group_name')->toArray();
            $message = $constraint['message'] ?? "You must select at least {$minTotal} total subjects from: " . implode(', ', $groupNames) . ". You selected {$totalCount}.";
            $this->errors[] = $message;
        }
    }

    /**
     * Validate center-specific subjects
     */
    protected function validateCenterSubjects(array $subjectCodes, string $centerNo)
    {
        $center = Center::with('subjects')->where('center_no', $centerNo)->first();

        if (!$center) {
            $this->warnings[] = "Center not found.";
            return;
        }

        $centerSubjects = $center->subjects->pluck('subject_code')->toArray();

        $normalizedCenterSubjects = array_map(fn($code) => str_pad((string) $code, 4, '0', STR_PAD_LEFT), $centerSubjects);
        $normalizedSelected = array_map(fn($code) => str_pad((string) $code, 4, '0', STR_PAD_LEFT), $subjectCodes);

        $invalidSubjects = array_diff($normalizedSelected, $normalizedCenterSubjects);

        if (!empty($invalidSubjects)) {
            $this->errors[] = "Some selected subjects are not available at your center: " . implode(', ', $invalidSubjects);
        }
    }
    /**
     * Validate incompatible subject pair combinations
     * 
     */
    protected function validateIncompatiblePairs(array $subjectCodes, SubjectGroupRule $rule)
    {
        $normalizedCodes = array_map(
            fn($code) => str_pad((string) $code, 4, '0', STR_PAD_LEFT),
            $subjectCodes
        );

        $incompatiblePairs = $rule->incompatible_pairs;

        foreach ($incompatiblePairs as $pair) {
            
            $subjectA = str_pad((string) ($pair['subject_a'] ?? $pair[0] ?? ''), 4, '0', STR_PAD_LEFT);
            $subjectB = str_pad((string) ($pair['subject_b'] ?? $pair[1] ?? ''), 4, '0', STR_PAD_LEFT);

            if (!$subjectA || !$subjectB)
                continue;

            if (in_array($subjectA, $normalizedCodes) && in_array($subjectB, $normalizedCodes)) {
                $this->errors[] = $pair['message'] ?? "Subject {$subjectA} and subject {$subjectB} cannot be selected together.";
            }
        }
    }

    /**
     * Get validation errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get validation warnings
     */
    public function getWarnings()
    {
        return $this->warnings;
    }
}