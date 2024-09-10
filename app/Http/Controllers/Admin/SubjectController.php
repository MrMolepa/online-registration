<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\Discipline;
use App\Models\FeeStracture;
use App\Models\Level;
use App\Models\OptionHeader;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sessions = Session::groupBy('session', 'financial_year')
            ->orderBy('financial_year', 'DESC')->get();
        $levels = Level::get();
        $disciplines = Discipline::get();
        $options = OptionHeader::get();
        $SubjectCode = DB::table('lgcsetimetable')->groupBy('subject_code')->get()->pluck('subject_code');
        if ($request->ajax()) {
            $subjects  =  Subject::with('sessions', 'selectedLevel');
            if ($request->level) {
                $subjects = $subjects->where('level', '=', $request->level);
            }
            $subjects = $subjects->get();
            return DataTables::of($subjects)
                ->setRowId('subject_code')
                ->editColumn('subject_code', function ($row) {
                    return   str_pad($row->subject_code, 4, '0', STR_PAD_LEFT);
                })
                ->editColumn('subject_name', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->subject_name</span>
                    <input class='editInput period form-control' type='text' name='subject_name' value='$row->subject_name'>
                    </div>";
                    return     $html;
                })
                ->editColumn('short_name', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'> $row->short_name</span>
                    <input class='editInput period form-control' type='text' name='short_name' value='$row->short_name'>
                    </div>";
                    return     $html;
                })
                ->editColumn('is_practical', function ($row) {
                    $status = $row->is_practical == 1 ? "Yes" : "No";
                    $statusHTML = $row->is_practical == 1 ?
                        "<option value='$row->is_practical' selected>Yes</option>
                        <option value='0'>No</option>
                        " :
                        "<option value='1'>Yes</option>
                        <option value='$row->is_practical' selected>No</option>
                        ";
                    $html = "<div class='form-group'>
                                <span class='editSpan period'>   $status </span>
                                <select id='is_practical' name='is_practical' class='editInput period form-control'>
                                   <option value=''>Please Select Status</option>
                                   $statusHTML
                                </select>
                            </div>";
                    return     $html;
                })
                ->editColumn('is_delf', function ($row) {
                    $status = $row->is_delf == 1 ? "Yes" : "No";
                    $statusHTML = $row->is_delf == 1 ?
                        "<option value='$row->is_delf' selected>Yes</option>
                        <option value='0'>No</option>
                        " :
                        "<option value='1'>Yes</option>
                        <option value='$row->is_delf' selected>No</option>
                        ";
                    $html = "<div class='form-group'>
                                <span class='editSpan period'>   $status </span>
                                <select id='is_practical' name='is_delf' class='editInput period form-control'>
                                   <option value=''>Please Select Status</option>
                                   $statusHTML
                                </select>
                            </div>";
                    return     $html;
                })
                ->editColumn('components', function ($row) {
                    $html = "
                     <a href='" . route('admin.components.index', ['subject_code' => $row->subject_code]) . "' class='btn btn-primary'>
                          components
                      </a>";
                    return     $html;
                })

                ->editColumn('level', function ($row) {
                    $levels = Level::get();
                    $levelHTML = "";
                    $selectedLevel = isset($row->selectedLevel) ? $row->selectedLevel->level : "";
                    foreach ($levels as  $level) {
                        if ($level->id == $row->level) {
                            $levelHTML .= "<option value='$level->id' selected >$level->level</option>";
                        } else {
                            $levelHTML .= "<option value='$level->id'>$level->level </option>";
                        }
                    }
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>$selectedLevel   </span>
                        <select class='editInput period form-control' name='level' >
                        <option value=''>Please select Level</option>
                            $levelHTML
                        </select>
                    </div>";
                    return     $html;
                })

                ->editColumn('discipline', function ($row) {
                    $disciplines = Discipline::get();
                    $disciplineHTML = "";
                    $selectedDiscipline = isset($row->selectedDiscipline) ? $row->selectedDiscipline->name : "";
                    foreach ($disciplines as  $discipline) {
                        if ($discipline->id == $row->discipline) {
                            $disciplineHTML .= "<option value='$discipline->id' selected >$discipline->name</option>";
                        } else {
                            $disciplineHTML .= "<option value='$discipline->id'>$discipline->name </option>";
                        }
                    }
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>$selectedDiscipline   </span>
                        <select class='editInput period form-control' name='discipline' >
                        <option value=''>Please select discipline</option>
                        $disciplineHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('session', function ($row) {
                    $sessions = Session::groupBy('session', 'financial_year')
                        ->orderBy('financial_year', 'DESC')->get();

                    $selectedSessionId = array();
                    $selectedSessionId = $row->sessions->pluck('id')->toArray();
                    $sessionHTML = "";
                    $selectedSession = "";
                    foreach ($sessions as  $session) {
                        if (in_array($session->id,  $selectedSessionId)) {
                            $selectedSession .= "$session->session-$session->financial_year, ";
                            $sessionHTML .= "<option value='$session->id' selected >$session->session-$session->financial_year </option>";
                        } else {
                            $sessionHTML .= "<option value='$session->id'>$session->session-$session->financial_year </option>";
                        }
                    }


                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>  $selectedSession   </span>
                        <select class='sessions-multiple editInput period form-control' name='sessions[]' multiple='multiple'>
                            $sessionHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('options', function ($row) {
                    $options = OptionHeader::get();
                    $selectedOptionId = array();
                    $selectedOptionId = $row->options->pluck('option_code')->toArray();
                    $optionHTML = "";
                    $selectedOption = "";
                    foreach ($options as  $option) {
                        if (in_array($option->option_code,  $selectedOptionId)) {
                            $selectedOption .= "$option->option_code , ";
                            $optionHTML .= "<option value='$option->option_code' selected >$option->option_code </option>";
                        } else {
                            $optionHTML .= "<option value='$option->option_code'>$option->option_code </option>";
                        }
                    }
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>   $selectedOption  </span>
                        <select class='options-multiple editInput period form-control' name='options[]' multiple='multiple'>
                        $optionHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.subjects.update', $row->subject_code) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.subjects.destroy', $row->subject_code) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['subject_name', 'options', 'components', 'short_name', 'level', 'discipline', 'is_practical', 'is_delf', 'session', 'action'])
                ->make(true);
        }
        return view('admin.subjects.subjects', compact('sessions', 'levels', 'disciplines', 'options',));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.subjects.create');
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
            'subject_code' => 'required|max:4|unique:subjects,subject_code',
            'subject_name' => 'required',
            'short_name' => 'required',
            'sessions' => 'required|array',
            'options' => 'required|array',
            'discipline' => 'required',
            'level' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $subject = new Subject();
        $subject->subject_code = $request->subject_code;
        $subject->subject_name = $request->subject_name;
        $subject->short_name = $request->short_name;
        $subject->level = $request->level;
        $subject->discipline = $request->discipline;
        $subject->save();
        $subject->sessions()->sync($request->sessions);
        $subject->options()->sync($request->options);
        return response()->json(['success' =>  'You have successfully added new records']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $center_no = $id;
        $center = Center::with('subjects.selectedLevel')->find($center_no);

        $level_id = null;
        $centerSubjects = null;
        $subjects = null;
        if (isset($center->subjects->first()->selectedLevel)) {
            $level_id =  $center->subjects->first()->selectedLevel->id;
            $centerSubjects = isset($center->subjects) ? $center->subjects->pluck('subject_code')->toArray() : array();
            $subjects = Subject::with('selectedLevel')->whereHas('selectedLevel', function ($query) use ($level_id) {
                $query->where('levels.id', '=', $level_id);
            })->get();
        } else {
            $level_id = Level::where('level', '=', $center->level)->first()->id;
            $centerSubjects = isset($center->subjects) ? $center->subjects->pluck('subject_code')->toArray() : array();
            $subjects = Subject::with('selectedLevel')->whereHas('selectedLevel', function ($query) use ($level_id) {
                $query->where('levels.id', '=', $level_id);
            })->get();
        }



        return view('admin.subjects.centerSubjects', compact('subjects', 'centerSubjects', 'center_no'));
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
            'subject_name' => 'required',
            'short_name' => 'required',
            'sessions' => 'required',
            'options' => 'required',
            'discipline' => 'required',
            'level' => 'required',
            'is_practical' => 'required',
            'is_delf' => 'required',


        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $subject = Subject::findOrFail($id);
        $subject->subject_name = $request->subject_name;
        $subject->short_name = $request->short_name;
        $subject->level = $request->level;
        $subject->discipline = $request->discipline;
        $subject->is_practical = $request->is_practical;
        $subject->is_delf = $request->is_delf;
        $subject->save();
        $subject->sessions()->sync($request->sessions);
        $subject->options()->sync($request->options);
        return response()->json(['success' =>  $subject]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
