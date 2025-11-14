<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CandidateProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * $return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {


            ini_set('memory_limit', '-1');
            set_time_limit(-1);
            $candidates =  Candidate::query();
            return DataTables::eloquent($candidates)
                ->setRowId('candidate_no')
                ->editColumn('candidate_no', function ($row) {
                    return   str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT);
                })
                ->editColumn('candidate_surname', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->candidate_surname</span>
                    <input class='editInput period form-control' type='text' name='candidate_surname' value='$row->candidate_surname'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('national_id', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->national_id</span>
                    <input class='editInput period form-control' type='text' name='national_id' value='$row->national_id'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('candidate_other_name', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->candidate_other_name</span>
                    <input class='editInput period form-control' type='text' name='candidate_other_name' value='$row->candidate_other_name'>
                    </div>";
                    return     $html;
                })
                ->editColumn('date_of_birth', function ($row) {
                    $newDate = date('Y-m-d', strtotime($row->date_of_birth));
                    $html = "<div class='form-group'>
                    <span class='editSpan period'>$newDate</span>
                    <input class='editInput period form-control' type='date' name='date_of_birth' value='$newDate'/>
                    </div>";
                    return     $html;
                })
                ->editColumn('gender', function ($row) {
                    // female
                    $female = ($row->gender == "F") ?  'selected' : '';
                    $male = ($row->gender == "M") ? 'selected' : '';

                    $html = "<div class='form-group'>
                    <span class='editSpan period'>$row->gender</span>
                    <select class='editInput period form-control' name='gender'>
                        <option value='F' $female  >Female</option>
                        <option value='M'  $male  >Male</option>
                    </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.candidate-profile.update', str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT)) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.candidate-profile.destroy', str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT)) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['candidate_surname', 'candidate_other_name', 'date_of_birth', 'national_id', 'gender', 'action'])
                ->toJson();
        }
        return view('admin.candidates.candidates-profile');
    }

    /**
     * Show the form for creating a new resource.
     *
     * $return \Illuminate\Http\Response
     */
    public function create()
    {
        $totalCandidte = Candidate::count();
        return view('admin.candidates.import', compact('totalCandidte'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * $param  \Illuminate\Http\Request  $request
     * $return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validationMassages = [];
        $validation_rules = [
            'candidate_no' => 'required|unique:candidates,candidate_no',
            'candidate_surname' => 'required',
            'candidate_other_name' => 'required',
            'national_id' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required|in:M,F',
        ];

        $validator = Validator::make($request->all(), $validation_rules, $validationMassages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $candidate_no = ($request->candidate_no == "*") ? getNextCandidateNumber() : $request->candidate_no;
        $request->merge(['candidate_no' => $candidate_no]);
        Candidate::create([
            'candidate_no' => $request->candidate_no,
            'candidate_surname' => strtoupper($request->candidate_surname),
            'national_id' => $request->national_id,
            'candidate_other_name' => strtoupper($request->candidate_other_name),
            'date_of_birth' => date("Y-m-d", strtotime($request->date_of_birth)),
            'gender' => $request->gender
        ]);

        return response()->json(['success' => "successfully added the record"]);
    }

    /**
     * Display the specified resource.
     *
     * $param  int  $id
     * $return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * $param  int  $id
     * $return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * $param  \Illuminate\Http\Request  $request
     * $param  int  $id
     * $return \Illuminate\Http\Response
     */


    public function updateCandidateNumber(Request $request)
    {

        $year = substr(date('Y'), 2, 4);
        $validator = Validator::make($request->all(), [
            'candidate_no' => 'required|max:9|min:9|exists:candidates,candidate_no',
            'new_candidate_no' => "required|max:9|min:9|exists:candidates,candidate_no",
            'national_id' => [
                'required',
                Rule::exists('candidates')->where(function ($query) use ($request) {
                    $query->orWhere('candidate_no', $request->candidate_no)
                        ->orWhere('candidate_no', $request->new_candidate_no);
                }),
            ]
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $old_candidate = $request->candidate_no;
        $new_candidate  = $request->new_candidate_no;
        $national_id  = $request->national_id;

        $financial_year = (date('m') <= 3) ?   (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        DB::statement("UPDATE  candidates SET  national_id =$national_id   WHERE candidate_no=$old_candidate");
        DB::statement("UPDATE  candidate_arrangement SET  candidate_no=$old_candidate  WHERE candidate_no= $new_candidate");
        DB::statement("UPDATE  center_candidate SET  candidate_no=$old_candidate  WHERE candidate_no= $new_candidate AND financial_year='$financial_year'");
        DB::statement("UPDATE  candidate_arrangement SET  candidate_no=$old_candidate  WHERE candidate_no= $new_candidate");
        DB::statement("UPDATE candidate_subject SET  candidate_no=$old_candidate  WHERE candidate_no= $new_candidate AND financial_year='$financial_year'");
        DB::statement("UPDATE guardians SET  candidate=$old_candidate  WHERE candidate= $new_candidate");
        DB::statement("UPDATE candidate_users SET  candidate_no=$old_candidate  WHERE candidate_no= $new_candidate");
        return response()->json(['success' =>  $old_candidate, 'new_candidate' =>  $old_candidate]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'candidate_surname' => 'required',
            'candidate_other_name' => 'required',
            'national_id' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required|in:M,F',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidate = Candidate::where('candidate_no', '=', $id)->first();
        $candidate->candidate_surname = $request->candidate_surname;
        $candidate->national_id = $request->national_id;
        $candidate->candidate_other_name = $request->candidate_other_name;
        $candidate->date_of_birth = date("Y-m-d", strtotime($request->date_of_birth));
        $candidate->gender = $request->gender;
        $candidate->save();


        DB::table('statement_achievements')
            ->where('candidate_no', $id)
            ->update([
                'candidate_surname'    => $request->candidate_surname,
                'candidate_other_name' => $request->candidate_other_name,
                'gender'               => $request->gender,
                'date_of_birth'    => date("Y-m-d", strtotime($request->date_of_birth)),
            ]);
        return response()->json(['success' => $candidate]);
    }


    public function import(Request $request)
    {
        set_time_limit(0);
        $this->validate($request, [
            'fileup' => 'required|mimes:csv,txt',
        ]);

        $file = file($request->fileup->getRealPath());

        $candidates = $this->csvToArray($file);
        $errors = [];
        $insertedCandidate = 0;
        for ($j = 0; $j < count($candidates); $j++) {

            $data = [
                'candidate_no' => $candidates[$j][0],
                'candidate_surname' => strtoupper($candidates[$j][1]),
                'candidate_other_name' => strtoupper($candidates[$j][2]),
                'date_of_birth' =>   date("Y-m-d", strtotime($candidates[$j][3])),
                'gender' => $candidates[$j][4]
            ];
            $validation_rules = [
                'candidate_no' => 'required|unique:candidates,candidate_no',
                'candidate_surname' => 'required|string',
                'candidate_other_name' => 'required|string',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender' => 'required|in:M,F',
            ];
            $validation_messages = [
                // add custom error messages
            ];
            $validator = Validator::make($data, $validation_rules, $validation_messages);

            if ($validator->fails()) {
                $error = [
                    'messages' =>  $validator->errors()->all(),
                    'row' => $j + 1,
                ];
                array_push($errors, $error);
            } else {
                $matchThese = ['candidate_no' => $data['candidate_no']];
                Candidate::updateOrCreate($matchThese, [
                    'candidate_no' => $data['candidate_no'],
                    'candidate_surname' => $data['candidate_surname'],
                    'candidate_other_name' => $data['candidate_other_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                ]);
                $insertedCandidate++;
            }
            $validator = null;
        }

        if (count($errors) > 0) {
            return back()->with(['errors-csv' => $errors, 'insertedCandidate' => $insertedCandidate, 'totalCandidates' => count($candidates)]);
        } else {
            return back()->with(['errors-csv' => $errors, 'insertedCandidate' => $insertedCandidate, 'totalCandidates' => count($candidates)]);
        }
    }


    private   function csvToArray($file)
    {
        $data = array_map('str_getcsv',  $file);
        return  $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * $param  int  $id
     * $return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->delete();
        return response()->json(['success' => 'Record deleted successfully']);
    }
}
