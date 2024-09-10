<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Invoice;
use App\Models\Level;
use App\Models\SubjectCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $levels = Level::query();
            return DataTables::eloquent($levels)
                ->setRowId('id')
                ->editColumn('level', function ($row) {
                    $html = "
                <div class='form-group'>
                <span class='editSpan period'> $row->level</span>
                <input class='editInput period form-control' type='text' name='level' value='$row->level'>
                </div>";
                    return     $html;
                })
                ->editColumn('description', function ($row) {
                    $html = "<div class='form-group'>
                                <span class='editSpan period'> $row->description</span>
                                <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                             </div>";
                    return $html;
                })
                ->editColumn('is_active', function ($row) {
                    $status = $row->is_active == 1 ? "Enabled" : "Disabled";
                    $statusHTML = $row->is_active == 1 ?
                        "<option value='$row->is_active' selected>Enabled</option>
                        <option value='0'>Disabled</option>
                        " :
                        "<option value='1'>Enabled</option>
                        <option value='$row->is_active' selected>Disabled</option>
                        ";
                    $html = "<div class='form-group'>
                                <span class='editSpan period'>   $status </span>
                                <select id='is_active' name='is_active' class='editInput period form-control'>
                                   <option value=''>Please Select Status</option>
                                   $statusHTML
                                </select>
                            </div>";
                    return     $html;
                })

                ->editColumn('private_registration', function ($row) {
                    $status = $row->private_registration == 1 ? "Show" : "Hide";
                    $statusHTML = $row->private_registration== 1 ?
                        "<option value='$row->private_registration' selected>Show</option>
                        <option value='0'>Hide</option>
                        " :
                        "<option value='1'>Show</option>
                        <option value='$row->is_active' selected>Hide</option>
                        ";
                    $html = "<div class='form-group'>
                                <span class='editSpan period'>   $status </span>
                                <select id='private_registration' name='private_registration' class='editInput period form-control'>
                                   <option value=''>Please Select Status</option>
                                   $statusHTML
                                </select>
                            </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                          <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.levels.update', $row->id) . "'> Save</button>
                          <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.levels.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['level', 'description','private_registration','is_active', 'action'])
                ->toJson();
        }
        $levels = Level::get();
        $center_no = $request->center_no;
        $centerLevel = Center::with('levels')->find($center_no)->levels->pluck('id')->toArray();
        return view('admin.levels.levels', compact('levels', 'center_no', 'centerLevel'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required',
            'description' => 'required',
            'is_active' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $level = new level();
        $level->description = $request->description;
        $level->is_active = $request->is_active;
        $level->level = $request->level;
        $level->save();
        return response()->json(['success' =>  'You have successfully added new records']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required',
            'description' => 'required',
            'is_active' => 'required',
            'private_registration' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $level = Level::findOrFail($id);
        $candidates = CenterCandidate::where([
            'level' => $level->level
        ])->count();
        $invoices = Invoice::where([
            'level' => $level->level
        ])->count();
        $candidateSubect = SubjectCandidate::where([
            'level' => $level->level
        ])->count();
        if ($candidates > 0 || $candidateSubect > 0 || $invoices > 0) {
            $level->description = $request->description;
            $level->is_active = $request->is_active;
            $level->private_registration= $request->private_registration;
            $level->save();
        } else {
            $level->level = $request->level;
            $level->description = $request->description;
            $level->is_active = $request->is_active;
            $level->private_registration= $request->private_registration;
            $level->save();
        }
        return response()->json(['success' =>  $level]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $level = Level::findOrFail($id);
        $candidates = CenterCandidate::where([
            'level' => $level->level
        ])->count();
        $invoices = Invoice::where([
            'level' => $level->level
        ])->count();
        $candidateSubject = SubjectCandidate::where([
            'level' => $level->level
        ])->count();
        if ($candidates >= 0 || $candidateSubject >= 0 || $invoices >= 0) {
            $level->delete();
            return response()->json(['success' =>  'You have successfully deleted  the records']);
        } else {
            return response()->json(['success' =>  "The records can not be deleted. "]);
        }
    }
}
