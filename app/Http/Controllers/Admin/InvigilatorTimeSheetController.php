<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\InvigilationProprietor;
use App\Models\InvigilationTimesheet;
use App\Models\InvigilatorProfile;
use App\Models\Level;
use App\Models\Session;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class InvigilatorTimeSheetController extends Controller
{
    public function index(Request $request)
    {





        if ($request->ajax()) {
            $timesheet = InvigilatorProfile::with('timesheet')
                ->whereHas('timesheet')->get();
            return DataTables::of($timesheet)
                ->addIndexColumn()
                ->editColumn('invigilator_sessions', function ($model) {
                    $invigilator_sessions = $model->timesheet;
                    $invigilator_sessions->map(function ($invigilator_session) {
                        $html = "<a href='javascript:void(0)' data-toggle='tooltip'  data-url='" . route('admin.invigilations.timesheet.destroy', $invigilator_session->pivot->id)  . "' data-original-title='Delete' class='delete-session btn btn-danger btn-sm fa fa-trash'></a>";
                        $invigilator_session['actions'] =     $html;
                        return $invigilator_session;
                    });
                    return   $invigilator_sessions;
                })
                ->addColumn('actions', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.timesheet.edit', $row->id)  . '" data-original-title="Edit" class="edit-session btn btn-primary btn-sm fa fa-edit"></a>';
                    return $btn;
                })
                ->rawColumns(['actions', 'invigilator_session'])
                ->make(true);
        }
        $invigilation_profiles = InvigilatorProfile::get();


        //invigilation_role
        $dates = Timetable::select(DB::raw('Date(date_time) as date_time'))
            ->groupBy(DB::raw('Date(date_time)'))
            ->where('session', 'November')
            ->where('level', 'LGCSE')
            ->orderBy(DB::raw('Date(date_time)'), 'asc')
            ->get();


        $years = DB::table('sessions')
            ->join('center_candidate', 'center_candidate.session', '=', 'sessions.session')
            ->select('sessions.id', 'sessions.session', 'sessions.financial_year')
            ->groupBy('sessions.session', 'sessions.financial_year')
            ->orderBy('sessions.financial_year', 'DESC')
            ->orderBy('sessions.session')
            ->get();

        $levels = Level::get();

        return view('admin.invigilation.timesheet.index', compact('invigilation_profiles', 'dates', 'years', 'levels'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required',
            'sessions' => 'required|array'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $profile = InvigilatorProfile::find($request->profile_id);
        $profile->timesheet()->sync($request->sessions);
        InvigilationProprietor::create(
            ['proprietor_source' => $request->profile_id],
            ['proprietor_target' => null]
        );
        return response()->json(['success' => 'Timesheet added successfully!']);
    }
    public function edit($id)
    {
        $invigilator = InvigilatorProfile::with('timesheet')
            ->whereHas('timesheet')->find($id);
        $center_no =  $invigilator->center_no;
        $invigilatorSession = $invigilator->timesheet->pluck('id')->toArray();
        $url = route('admin.invigilations.timesheet.update', $id);
        $session =  $invigilator->session;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $subjects = $center->subjects->pluck('subject_code')->toArray();
        $timetables = Timetable::select('id', 'subject_code', 'paper_no', 'subject_name', 'date_time')
            ->where('session', '=',  $session)
            ->whereIn('subject_code', $subjects)
            ->orderBy('date_time')
            ->get();

        $timetableHtml = '';
        foreach ($timetables  as   $timetable) {
            $exams_date = date('Y-M-d H:i', strtotime($timetable->date_time));
            $cheched = in_array($timetable->id, $invigilatorSession) ? 'checked' : '';
            $timetableHtml .= "<div class='form-check col-md-6'>
                        <input class='form-check-input' type='checkbox' name='sessions[]'    $cheched value='$timetable->id' id='session_$timetable->paper_no-$timetable->subject_code'>
                        <label class='form-check-label' for='session_$timetable->paper_no-$timetable->subject_code'>$timetable->subject_code $timetable->paper_no    $exams_date </label>
                    </div>";
        }


        return response()->json(['sessions' => $timetableHtml, 'url' => $url, 'invigilator' => $invigilator]);
    }

    public function update(Request $request, $id)
    {

        $validate = Validator::make($request->all(), [
            'profile_id' => 'required',
            'sessions' => 'required|array'
        ], [
            'profile_id.required' => 'The national ID field is required.',
            'profile_id.integer' => 'The national ID must be an integer.',
            'sessions.required' => 'The date field is required.',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' =>  $validate->errors()]);
        } else {
            $profile = InvigilatorProfile::find($request->profile_id);
            $profile->timesheet()->sync($request->sessions);
            return response()->json(['success' => 'Timesheet updated successfully!']);
        }
    }

    public function destroy($id)
    {

        $invigilatorSession = InvigilationTimesheet::find($id);
        $invigilatorSession->delete();
        return response()->json(['success' => 'Timesheet deleted successfully.']);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|mimes:csv,txt',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        set_time_limit(0);
        $file = file($request->upload->getRealPath());
        $timesheet = $this->csvToArray($file);
        $errors = [];
        $insertedcount = 0;
        foreach ($timesheet as $row) {
            if (!str_contains($row[0], 'national_id')) {
                $data = [
                    'national_id' => $row[0],
                    'other_names' => strtolower($row[1]),
                    'surname' => strtolower($row[2]),
                    'session' => $row[3],
                    'subject_code' => $row[4],
                    'subject_name' => strtolower($row[5]),
                    'paper_no' => strtolower($row[6]),
                    'exam_date' =>   date('Y-m-d', strtotime($row[7])),
                ];

                $validator = Validator::make($data, [
                    'national_id' => [
                        'required',
                        'string',
                        Rule::exists('invigilator_profile')
                            ->where(function ($query) use ($data) {
                                return $query->where('invigilator_profile.national_id', $data['national_id'])
                                    ->where('invigilator_profile.session', $data['session']);
                            }),
                        Rule::notIn($this->validateNatioanID($data)),
                        Rule::in($this->is_session($data))
                    ],
                    'other_names' => ['required'],
                    'surname' => ['required'],
                    'subject_code' => ['required', 'exists:timetable,subject_code'],
                    'subject_name' => ['required'],
                    'paper_no' => ['required', 'exists:timetable,paper_no'],
                    'exam_date' => ['required', 'date', Rule::in($this->validateExamDates($data))],
                ], [
                    'national_id.not_in' => 'The session already registered',
                    'national_id.in' => 'The invigilator is not session based'
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'messages' =>  $validator->errors()->all(),
                        'row' => $row,
                    ];
                } else {

                    $profile = InvigilatorProfile::where('national_id', $data['national_id'])->first();
                    $timetables = Timetable::whereDate('date_time', date('Y-m-d', strtotime($data['exam_date'])))
                        ->where('paper_no', $data['paper_no'])
                        ->where('subject_code', $data['subject_code'])
                        ->get()->pluck('id')->toArray();
                    $profile->timesheet()->attach($timetables);



                    $validator = null;
                    $insertedcount++;
                }
            }
        }

        return response()->json([
            'status' => count($errors) > 0 ? 'error' : 'success',
            'errors' => $errors,
            'totalCandidates' => count($timesheet),
            'insertedCandidates' => $insertedcount,
        ]);
    }
    private function csvToArray($file)
    {
        $data = array_map('str_getcsv',  $file);
        return  $data;
    }

    public function getSubjects(Request $request)
    {
        $invigilator = InvigilatorProfile::find($request->profile_id);
        $session =  $invigilator->session;
        $center_no =  $invigilator->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $subjects = $center->subjects->pluck('subject_code')->toArray();
        $timetables = Timetable::select('id', 'subject_code', 'paper_no', 'subject_name', 'date_time')
            ->where('session', '=',  $session)
            ->whereIn('subject_code', $subjects)
            ->orderBy('date_time')
            ->get();
            $timetableHtml = '';
            foreach ($timetables  as   $timetable) {
                $exams_date = date('Y-M-d H:i', strtotime($timetable->date_time));
                $timetableHtml .= "<div class='form-check col-md-6'>
                            <input class='form-check-input' type='checkbox' name='sessions[]'  value='$timetable->id' id='session_$timetable->paper_no-$timetable->subject_code'>
                            <label class='form-check-label' for='session_$timetable->paper_no-$timetable->subject_code'>$timetable->subject_code $timetable->paper_no    $exams_date </label>
                        </div>";
            }

        return response()->json(['sessions' => $timetableHtml]);
    }

    private function validateExamDates($data)
    {
        $timetable = Timetable::select([
            DB::raw('date(date_time) as exam_date')
        ])
            ->where('subject_code', $data['subject_code'])
            ->where('session', $data['session'])
            ->whereDate('date_time', date('Y-m-d', strtotime($data['exam_date'])))
            ->where('paper_no', $data['paper_no'])
            ->get();
        if ($timetable) {
            return   $timetable->pluck('exam_date')->toArray();
        }
        return   [];
    }

    private function validateNatioanID($data)
    {
        $invigilatorProfile = InvigilatorProfile::whereHas('timesheet', function ($q) use ($data) {
            $q->where('subject_code', '=', $data['subject_code'])
                ->where('paper_no', '=', $data['paper_no']);
        })
            ->where('session', $data['session'])
            ->where('national_id', $data['national_id'])
            ->get();

        if ($invigilatorProfile) {
            return   $invigilatorProfile->pluck('national_id')->toArray();
        }
        return [];
    }
    private function is_session($data)
    {


        $invigilatorProfile = InvigilatorProfile::whereHas('invigilation_role', function ($q) {
            $q->where('is_sessions', '=', 1);
        })
            ->where('session', $data['session'])
            ->where('national_id', $data['national_id'])
            ->get();
        if ($invigilatorProfile) {
            return   $invigilatorProfile->pluck('national_id')->toArray();
        }
        return [];
    }
}
