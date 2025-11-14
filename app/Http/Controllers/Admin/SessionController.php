<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\FeeGroup;
use App\Models\FeeStracture;
use App\Models\Invoice;
use App\Models\Session;
use App\Models\Subject;
use App\Models\SubjectCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {




        if ($request->ajax()) {
            $sessions = Session::query();
            return DataTables::eloquent($sessions)
                ->setRowId('id')
                ->editColumn('session', function ($row) {
                    $html = "
                <div class='form-group'>
                <span class='editSpan period'> $row->session</span>
                <input class='editInput period form-control' type='text' name='session' value='$row->session'>
                </div>";
                    return     $html;
                })
                ->editColumn('description', function ($row) {
                    $html = "<div class='form-group'>
                <span class='editSpan period'> $row->description</span>
                <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                </div>";
                    return     $html;
                })
                ->editColumn('financial_year', function ($row) {
                    $html = "<div class='form-group'>
                <span class='editSpan period'> $row->financial_year</span>
                <input class='editInput period form-control' type='text' name='financial_year' value='$row->financial_year'>
                </div>";
                    return     $html;
                })->editColumn('financial_closing_date', function ($row) {
                    $date = date('Y-m-d', strtotime($row->financial_closing_date));
                    $html = "<div class='form-group'>
                            <span class='editSpan period'>  $date</span>
                            <input class='editInput period form-control' type='date' name='financial_closing_date' value='$date'>
                            </div>";
                    return     $html;
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
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                          <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.sessions.update', $row->id) . "'> Save</button>
                          <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.sessions.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['session', 'description', 'financial_year', 'financial_closing_date', 'is_active', 'action'])
                ->toJson();
        }
        $sessions = Session::groupBy('session')->get()->pluck('session')->toArray();

        $center_no = $request->center_no;
        $center = Center::find($center_no);
        $centerSessions = json_decode($center->sessions, true);
        return view('admin.sessions.sessions', compact('center_no', 'sessions', 'centerSessions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exams_month' => 'required',
            'financial_closing' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        // Exams Month June or November
        $exams_month = $request->exams_month;
        $session_month = date('F', strtotime('01.' .  ((int)$exams_month + 1) . '.2025'));
        // Level
        $financial_year =  date('Y') . '-' . (date('Y') + 1);
        $last_financial_year = (date('Y') - 1) . '-' . date('Y');
        $session = new Session();
        $session->session = $session_month;
        $session->financial_closing_date =  date("Y-m-d", strtotime($request->financial_closing));
        $session->description = date('F', strtotime('01.' . $exams_month . '.2025')) . "/" . date('F', strtotime('01.' . ((int)$exams_month + 1) . '.2001'));
        $session->is_active = $request->is_active;
        $session->financial_year = $financial_year;
        $session->save();
        $newsession_id =  $session->id;


        // sync subjects
        $previousSession = Session::with('subjects')->where('financial_year', '=', $last_financial_year)
            ->where('session', '=', $session_month)->first();
        $subjects = $previousSession->subjects->pluck('subject_code')->toArray();
        $session->subjects()->sync($subjects);
        // Fees
        $feegroups = FeeGroup::with('session', 'level', 'feetypes')
            ->whereHas('session', function ($query) use ($last_financial_year, $session_month) {
                $query->where('financial_year', '=', $last_financial_year)
                    ->where('session', '=', $session_month);
            })->get();
        foreach ($feegroups  as  $feegroup) {
            $model = $feegroup;
            $newModel = $model->replicate()->fill([
                'session_id' => $newsession_id,
            ]);

            $newModel->push();
            // Once the model has been saved with a new ID, we can get its children
            foreach ($newModel->getRelations() as $relation => $items) {
                if ($relation == 'feetypes') {
                    foreach ($items as $item) {
                        // Now we get the extra attributes from the pivot tables, but
                        // we intentionally leave out the foreignKey, as we already
                        // have it in the newModel

                        $keys = [];
                        array_push($keys, $item->pivot->getForeignKey());
                        array_push($keys, $item->pivot->getKeyName());
                        $extra_attributes = Arr::except($item->pivot->getAttributes(),   $keys);
                        $newModel->{$relation}()->attach($item, $extra_attributes);
                    }
                }
            }

            $newModel->save();
        }
        return response()->json(['success' =>  'You have successfully created  the records']);
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
            'session' => 'required',
            'description' => 'required',
            'is_active' => 'required',
            'financial_closing_date' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $session = Session::findOrFail($id);
        $candidates = CenterCandidate::where([
            'financial_year' => $session->financial_year,
            'session' => $session->session
        ])->count();
        $invoices = Invoice::where([
            'financial_year' => $session->financial_year,
            'session' => $session->session
        ])->count();
        $candidateSubect = SubjectCandidate::where([
            'financial_year' => $session->financial_year,
            'session' => $session->session
        ])->count();
        $date = date('Y-m-d', strtotime($request->financial_closing_date));
        if ($candidates > 0 || $candidateSubect > 0 || $invoices > 0) {
            $session->financial_closing_date =  $date;
            $session->description = $request->description;
            $session->is_active = $request->is_active;
            $session->save();
        } else {
            $session->session = $request->session;
            $session->financial_closing_date = $date;
            $session->description = $request->description;
            $session->is_active = $request->is_active;
            $session->save();
        }

        return response()->json(['success' => $session]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $session = Session::findOrFail($id);
        $candidates = CenterCandidate::where([
            'financial_year' => $session->financial_year,
            'session' => $session->session
        ])->count();
        $invoices = Invoice::where([
            'financial_year' => $session->financial_year,
            'session' => $session->session
        ])->count();
        $candidateSubect = SubjectCandidate::where([
            'financial_year' => $session->financial_year,
            'session' => $session->session
        ])->count();
        if ($candidates > 0 || $candidateSubect > 0 || $invoices > 0) {
            return response()->json(['error' => 'Session can not be deleted because has candidates']);;
        } else {
            $session->delete();
            return response()->json(['success' =>  'You have successfully deleted  the records']);
        }
    }
}
