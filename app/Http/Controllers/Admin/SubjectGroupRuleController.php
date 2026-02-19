<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectGroupRule;
use App\Models\Level;
use App\Models\SubjectGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SubjectGroupRuleController extends Controller
{
    /**
     * Get available groups for AJAX
     */
    public function getGroups(Request $request)
    {
        Log::info('getGroups called with level_id: ' . $request->input('level_id'));

        $levelId = $request->input('level_id');

        if (!$levelId) {
            return response()->json(['groups' => [], 'message' => 'No level_id provided']);
        }

        try {
            $groups = SubjectGroup::where('is_active', true)
                ->where('level_id', $levelId)
                ->with([
                    'subjects' => function ($query) {
                        $query->select('subjects.subject_code', 'subjects.subject_name');
                    }
                ])
                ->get(['id', 'group_code', 'group_name', 'level_id']);

            Log::info('Groups found: ' . $groups->count());


            return response()->json([
                'success' => true,
                'groups' => $groups,
                'count' => $groups->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getGroups: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'groups' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $levels = Level::where('is_active', true)->get();

        if ($request->ajax()) {
            $rules = SubjectGroupRule::with('level');

            if ($request->level_id) {
                $rules = $rules->where('level_id', $request->level_id);
            }

            $rules = $rules->get();

            return DataTables::of($rules)
                ->setRowId('id')
                ->addColumn('rule_name', function ($row) {
                    return $row->rule_name;
                })
                ->addColumn('level', function ($row) {
                    return $row->level ? $row->level->level : 'N/A';
                })
                ->addColumn('type', function ($row) {
                    return $row->type_name;
                })
                ->addColumn('is_active', function ($row) {
                    $status = $row->is_active ? "Active" : "Inactive";
                    return "<span class='label label-" . ($row->is_active ? 'success' : 'danger') . "'>{$status}</span>";
                })
                ->addColumn('action', function ($row) {
                    $html = "
                    <button type='button' class='btn btn-sm btn-primary editRuleBtn' data-id='{$row->id}'><i class='fas fa-edit'></i> Edit</button>
                    <button type='button' class='btn btn-sm btn-danger deleteRuleBtn' data-url='" . route('admin.subject-group-rules.destroy', $row->id) . "'><i class='fas fa-trash'></i> Delete</button>";
                    return $html;
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('admin.subject-group-rules.index-content', compact('levels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Store method called', $request->all());

        $validator = Validator::make($request->all(), [
            'rule_name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id',
            'type' => 'required|in:1,2,3',
            'rules' => 'required|json',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            $rulesData = json_decode($request->rules, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format for rules'
                ]);
            }

            $rule = SubjectGroupRule::create([
                'rule_name' => $request->rule_name,
                'level_id' => $request->level_id,
                'type' => $request->type,
                'rules' => $rulesData,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            Log::info('Rule created successfully', ['id' => $rule->id]);

            return response()->json([
                'success' => true,
                'message' => 'Rule created successfully',
                'rule' => $rule
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating rule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating rule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $rule = SubjectGroupRule::with('level')->findOrFail($id);
        $levels = Level::where('is_active', true)->get();
        $groups = SubjectGroup::where('is_active', true)
            ->where('level_id', $rule->level_id)
            ->with('subjects')
            ->get();

        // Ensure rules is properly formatted
        $rulesData = $rule->rules;
        if (is_string($rulesData)) {
            $rulesData = json_decode($rulesData, true);
        }

        // Ensure all required keys exist
        $formattedRules = [
            'min_subjects' => $rulesData['min_subjects'] ?? null,
            'max_subjects' => $rulesData['max_subjects'] ?? null,
            'required_groups' => $rulesData['required_groups'] ?? [],
            'forbidden_groups' => $rulesData['forbidden_groups'] ?? [],
            'group_constraints' => $rulesData['group_constraints'] ?? [],
            'incompatible_pairs' => $rulesData['incompatible_pairs'] ?? [],
        ];

        $ruleData = [
            'id' => $rule->id,
            'rule_name' => $rule->rule_name,
            'level_id' => $rule->level_id,
            'type' => $rule->type,
            'is_active' => $rule->is_active,
            'rules' => $formattedRules,
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'rule' => $ruleData,
                'levels' => $levels,
                'groups' => $groups
            ]);
        }

        return view('admin.subject-group-rules.edit', compact('rule', 'levels', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rule_name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id',
            'type' => 'required|in:1,2,3',
            'rules' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            $rule = SubjectGroupRule::findOrFail($id);

            $rulesData = json_decode($request->rules, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format for rules'
                ]);
            }

            $rule->update([
                'rule_name' => $request->rule_name,
                'level_id' => $request->level_id,
                'type' => $request->type,
                'rules' => $rulesData,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rule updated successfully',
                'rule' => $rule
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating rule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating rule: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $rule = SubjectGroupRule::findOrFail($id);
            $rule->delete();

            return response()->json([
                'success' => 'Rule deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error deleting rule: ' . $e->getMessage()
            ]);
        }
    }
}