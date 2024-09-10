<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStracture;
use App\Models\Level;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class FeesController extends Controller
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

        if ($request->ajax()) {
            $fees = FeeStracture::with('selectedSession', 'selectedLevel')
                ->orderBy('candidate_type', 'ASC')
                ->orderBy('financial_year', 'ASC')
                ->get();
            return DataTables::of($fees)
                ->setRowId('id')
                ->editColumn('candidate_type', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->candidate_type</span>
                    <input class='editInput period form-control' type='text' name='candidate_type' value='$row->candidate_type'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('session', function ($row) {
                    $sessions = Session::where('financial_year', '=', $row->financial_year)->get();
                    $sessionHTML = "";
                    $selectedSession = isset($row->selectedSession->session) ? $row->selectedSession->session : " ";
                    foreach ($sessions as  $session) {
                        if ($session->id == $row->session) {
                            $sessionHTML .= "<option value='$session->id' selected >$session->session</option>";
                        } else {
                            $sessionHTML .= "<option value='$session->id'>$session->session </option>";
                        }
                    }
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'> $selectedSession   </span>
                        <select class='editInput period form-control' name='session' >
                        <option value=''>Please select session</option>
                           $sessionHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('level', function ($row) {
                    $levels = Level::get();
                    $levelHTML = "";
                    $selectedLevel = isset($row->selectedLevel->level) ? $row->selectedLevel->level : " ";
                    foreach ($levels as  $level) {
                        if ($level->id == $row->level) {
                            $levelHTML .= "<option value='$level->id' selected >$level->level</option>";
                        } else {
                            $levelHTML .= "<option value='$level->id'>$level->level </option>";
                        }
                    }
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'> $selectedLevel   </span>
                        <select class='editInput period form-control' name='level' >
                        <option value=''>Please select Level</option>
                            $levelHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('subject_fee', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->subject_fee</span>
                    <input class='editInput period form-control' type='text' name='subject_fee' value='$row->subject_fee'>
                    </div>";
                    return     $html;
                })
                ->editColumn('registration_fee', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->registration_fee</span>
                    <input class='editInput period form-control' type='text' name='registration_fee' value='$row->registration_fee'>
                    </div>";
                    return     $html;
                })
                ->editColumn('local_fee', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->local_fee</span>
                    <input class='editInput period form-control' type='text' name='local_fee' value='$row->local_fee'>
                    </div>";
                    return     $html;
                })

                ->editColumn('delf_fee', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->delf_fee</span>
                    <input class='editInput period form-control' type='text' name='delf_fee' value='$row->delf_fee'>
                    </div>";
                    return     $html;
                })
                ->editColumn('practical_subject_fee', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->practical_subject_fee</span>
                    <input class='editInput period form-control' type='text' name='practical_subject_fee' value='$row->practical_subject_fee'>
                    </div>";
                    return     $html;
                })
                ->editColumn('bank_charge', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->bank_charge</span>
                    <input class='editInput period form-control' type='text' name='bank_charge' value='$row->bank_charge'>
                    </div>";
                    return     $html;
                })
                ->editColumn('financial_year', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->financial_year</span>
                    <input class='editInput period form-control' type='text' name='financial_year' value='$row->financial_year'>
                    </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.fees.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.fees.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns([
                    'candidate_type',
                    'session',
                    'delf_fee',
                    'level',
                    'subject_fee',
                    'registration_fee',
                    'local_fee',
                    'practical_subject_fee',
                    'bank_charge',
                    'financial_year',
                    'action'
                ])
                ->make(true);
        }
        return view('admin.finance.fees.fees', compact('sessions', 'levels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
            'candidate_type' => [
                'required', Rule::unique('fees_stracture')->where(function ($query) use ($request) {
                    return $query->where('candidate_type', $request->input('candidate_type'))
                        ->where('financial_year', $request->input('financial_year'));
                })
            ],
            'subject_fee' =>  ['required'],
            'registration_fee' =>  ['required'],
            'local_fee' =>  ['required'],
            'practical_subject_fee' =>  ['required'],
            'delf_fee' =>  ['required'],
            'bank_charge' =>  ['required'],
            'level' =>  ['required'],
            'session' =>  ['required'],
            'financial_year' =>  ['required']
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $session = Session::where('financial_year', '=', $request->financial_year)
            ->where('id', '=', $request->session)->count();

        if ($session > 0) {

            //delf_fee
            $fee = new FeeStracture();
            $fee->candidate_type = $request->candidate_type;
            $fee->subject_fee = $request->subject_fee;
            $fee->session = $request->session;
            $fee->level = $request->level;
            $fee->registration_fee = $request->registration_fee;
            $fee->local_fee = $request->local_fee;
            $fee->delf_fee= $request-> delf_fee;
            $fee->practical_subject_fee = $request->practical_subject_fee;
            $fee->bank_charge = $request->bank_charge;
            $fee->financial_year = $request->financial_year;
            $fee->save();
            return response()->json(['success' => 'Successfull']);
        } else {
            return response()->json(['errors' => ['session' => ['Invalid Session']]]);
        }
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
            'candidate_type' => 'required',
            'subject_fee' => 'required',
            'registration_fee' => 'required',
            'local_fee' => 'required',
            'session' =>  ['required'],
            'delf_fee' =>  ['required'],
            'level' =>  ['required'],
            'practical_subject_fee' => 'required',
            'bank_charge' => 'required',
            'financial_year' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $fee = FeeStracture::findOrFail($id);
        $fee->candidate_type = $request->candidate_type;
        $fee->subject_fee = $request->subject_fee;
        $fee->session = $request->session;
        $fee->level = $request->level;
        $fee->delf_fee = $request->delf_fee;
        $fee->registration_fee = $request->registration_fee;
        $fee->local_fee = $request->local_fee;
        $fee->practical_subject_fee = $request->practical_subject_fee;
        $fee->bank_charge = $request->bank_charge;
        $fee->financial_year = $request->financial_year;
        $fee->save();
        return response()->json(['success' => 'Successfully deleted']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fee = FeeStracture::findOrFail($id);
        $fee->delete();
        return response()->json(['success' => 'Successfully removed the records']);
    }
}
