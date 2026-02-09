<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectGroup;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SubjectGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $levels = Level::where('is_active', true)->get();
        $subjects = Subject::all();

        if ($request->ajax()) {
            $groups = SubjectGroup::with(['level', 'subjects']);

            if ($request->level_id) {
                $groups = $groups->where('level_id', $request->level_id);
            }


            $groups = $groups->get();

            return DataTables::of($groups)
                ->setRowId('id')
                ->addColumn('group_code', function ($row) {
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>{$row->group_code}</span>
                        <input class='editInput period form-control' type='text' name='group_code' value='{$row->group_code}'>
                    </div>";
                    return $html;
                })
                ->addColumn('group_name', function ($row) {
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>{$row->group_name}</span>
                        <input class='editInput period form-control' type='text' name='group_name' value='{$row->group_name}'>
                    </div>";
                    return $html;
                })
                ->addColumn('level', function ($row) {
                    $levels = Level::where('is_active', true)->get();
                    $levelHTML = "";
                    $selectedLevel = $row->level ? $row->level->level : "";

                    foreach ($levels as $level) {
                        $selected = $level->id == $row->level_id ? 'selected' : '';
                        $levelHTML .= "<option value='{$level->id}' {$selected}>{$level->level}</option>";
                    }

                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>{$selectedLevel}</span>
                        <select class='editInput period form-control' name='level_id'>
                            <option value=''>Select Level</option>
                            {$levelHTML}
                        </select>
                    </div>";
                    return $html;
                })
                ->addColumn('subjects', function ($row) {
                    $subjectCodes = $row->subjects->pluck('subject_code')->toArray();
                    $subjectNames = $row->subjects->pluck('subject_name')->toArray();
                    $displayText = implode(', ', $subjectNames);

                    $subjects = Subject::all();
                    $subjectsHTML = "";
                    foreach ($subjects as $subject) {
                        $checked = in_array($subject->subject_code, $subjectCodes) ? 'checked' : '';
                        $subjectsHTML .= "<div class='checkbox'><label><input type='checkbox' name='subjects[]' value='{$subject->subject_code}' {$checked}> {$subject->subject_name}</label></div>";
                    }

                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>{$displayText}</span>
                        <div class='editInput period' style='max-height: 200px; overflow-y: auto;'>{$subjectsHTML}</div>
                    </div>";
                    return $html;
                })
                ->addColumn('is_active', function ($row) {
                    $status = $row->is_active ? "Active" : "Inactive";
                    $statusHTML = $row->is_active ?
                        "<option value='1' selected>Active</option><option value='0'>Inactive</option>" :
                        "<option value='1'>Active</option><option value='0' selected>Inactive</option>";

                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>{$status}</span>
                        <select class='editInput period form-control' name='is_active'>
                            {$statusHTML}
                        </select>
                    </div>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.subject-groups.show', $row->id);
                    $updateUrl = route('admin.subject-groups.update', $row->id);
                    $deleteUrl = route('admin.subject-groups.destroy', $row->id);

                    $html = "
                     <button type='button' class='btn btn-sm btn-primary editBtn' data-id='{$row->id}' data-url='{$editUrl}'><i class='fas fa-edit'></i> Edit</button>
                     <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='{$deleteUrl}'><i class='fas fa-trash'></i> Delete</button>";
                    return $html;
                })
                ->rawColumns(['group_code', 'group_name', 'level', 'subjects', 'is_active', 'action'])
                ->make(true);
        }

        return view('admin.subject-groups.index-content', compact('levels', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_code' => 'required|unique:subject_groups,group_code',
            'group_name' => 'required',
            'level_id' => 'required|exists:levels,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,subject_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $group = SubjectGroup::create([
            'group_code' => $request->group_code,
            'group_name' => $request->group_name,
            'level_id' => $request->level_id,
            'is_active' => $request->is_active ?? true,
        ]);

        $group->subjects()->sync($request->subjects);

        return response()->json(['success' => 'Subject group created successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'group_code' => 'required|unique:subject_groups,group_code,' . $id,
            'group_name' => 'required',
            'level_id' => 'required|exists:levels,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,subject_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $group = SubjectGroup::findOrFail($id);
        $group->update([
            'group_code' => $request->group_code,
            'group_name' => $request->group_name,
            'level_id' => $request->level_id,
            'is_active' => $request->is_active ?? $group->is_active,
        ]);

        $group->subjects()->sync($request->subjects);

        //return response()->json(['success' => $group]);
        return response()->json(['success' => 'Subject group updated successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $group = SubjectGroup::with(['level', 'subjects'])->findOrFail($id);

        return response()->json([
            'id' => $group->id,
            'group_code' => $group->group_code,
            'group_name' => $group->group_name,
            'level_id' => $group->level_id,
            'is_active' => $group->is_active,
            'subjects' => $group->subjects->pluck('subject_code')->toArray()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $group = SubjectGroup::findOrFail($id);
        $group->subjects()->detach();
        $group->delete();

        return response()->json(['success' => 'Subject group deleted successfully']);
    }
}