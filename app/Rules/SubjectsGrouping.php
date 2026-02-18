<?php

namespace App\Rules;

use App\Models\Center;
use App\Models\Level;
use App\Services\SubjectValidator;
use Illuminate\Contracts\Validation\Rule;

class SubjectsGrouping implements Rule
{
    private $centerNo;
    private $validationErrors = [];

    public function __construct($centerNo = null)
    {
        $this->centerNo = $centerNo;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Get center and level information
        $center = Center::with('subjects')->where('center_no', '=', $this->centerNo)->first();

        if (!$center) {
            $this->validationErrors[] = "Center not found";
            return false;
        }

        $levelName = $center->level;
        $level = Level::where('level', $levelName)->first();

        if (!$level) {
            $this->validationErrors[] = "Level not found";
            return false;
        }

        // Extract subject codes and type from value
        $subjectCodes = $this->changeToSubjects($value);
        $type = isset($value[0]['type']) ? $value[0]['type'] : 1;

        // Use SubjectValidator service
        $validator = new SubjectValidator();
        $result = $validator->validate(
            $subjectCodes,
            $level->id,
            $type,
            $this->centerNo
        );

        if (!$result['valid']) {
            $this->validationErrors = $result['errors'];
            return false;
        }

        // Store warnings if any
        if (!empty($result['warnings'])) {
            // You can log warnings or handle them as needed
        }

        return true;
    }

    /**
     * Convert subject array to subject codes
     */
    private function changeToSubjects($subjects)
    {
        $newArray = array();
        foreach ($subjects as $key => $subject) {
            if (isset($subject['subject_code'])) {
                $newArray[] = str_pad((string) $subject['subject_code'], 4, '0', STR_PAD_LEFT);
            }
        }
        return $newArray;
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (!empty($this->validationErrors)) {
            return implode(' ', $this->validationErrors);
        }

        $center = Center::where('center_no', '=', $this->centerNo)->first();
        $level = $center ? $center->level : 'Unknown';

        return "Invalid {$level} Examinations Subjects Grouping";
    }
}