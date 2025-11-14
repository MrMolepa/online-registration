<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Libraries\Payment\Payment;
use App\Mail\InvigilatorMail;
use App\Models\Center;
use App\Models\InvigilationCandidate;
use App\Models\InvigilationRole;
use App\Models\InvigilationStatus;
use App\Models\InvigilationType;
use App\Models\InvigilatorExperience;
use App\Models\InvigilatorProfile;
use App\Models\CenterCandidate;
use App\Models\InvigilationCatergories;
use App\Models\Level;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
use App\Libraries\fpdfcertificate\exFPDF;
use App\Models\District;
use App\Models\InvigilationPaymentMethod;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use PHPMailer\PHPMailer\PHPMailer;
use setasign\Fpdi\Fpdi;


class InvigilationListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {



        if ($request->ajax()) {
            $level = empty($request->level) ? 'LGCSE' : $request->level;
            $sponsor = $request->sponsor;
            $catergory = empty($request->catergory) ? '1' : $request->catergory;
            $center_no = $request->center_no;
            $year = empty($request->year) ? '2024-2025' : $request->year;
            $session = empty($request->session) ? 'November' : $request->session;
            $status = $request->status;

            if ($request->has('center_filter')) {
                $centers = DB::table('centers')
                    ->select('centers.center_no', 'centers.center_name')
                    ->join('center_candidate', 'centers.center_no', '=', 'center_candidate.center_no')
                    ->groupBy('center_candidate.center_no');
                if (!empty($year)) {
                    $centers = $centers->where('center_candidate.financial_year', '=', $year);
                }
                if (!empty($level)) {
                    $centers = $centers->where('center_candidate.level', '=', $level);
                }
                if (!empty($sponsor)) {
                    $centers = $centers->where('center_candidate.sponser', '=',  $sponsor);
                }

                if (!empty($session)) {
                    $centers = $centers->where('center_candidate.session', '=',  $session);
                }
                if (!empty($center_no)) {
                    $center_no = $center_no;
                    $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
                    $centerSessions = json_decode($center->sessions, true);
                    $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
                    $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)
                        ->where('is_active', '=', 1)
                        ->first();

                    $totalcandidates =  Payment::highestSubject($center_no, $center->level,  $session->session, $session->financial_year);;
                    if (!empty($catergory)) {
                        $invigilation_types = DB::table('invigilation_candidates')
                            ->select(
                                'invigilation_candidates.range_start',
                                'invigilation_candidates.range_end',
                                'invigilation_types.name',
                                DB::raw("invigilation_catergories.name as catergory_name"),
                                'invigilation_roles.id',
                                'invigilation_roles.invigilator_number'
                            )
                            ->leftJoin('invigilation_roles', 'invigilation_roles.invigilation_candidate_id', '=', 'invigilation_candidates.id')
                            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
                            ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
                            ->where('invigilation_candidates.range_start', '<=', $totalcandidates)
                            ->where('invigilation_candidates.range_end', '>=', $totalcandidates)
                            ->where('invigilation_catergories.id', '=', $catergory)
                            ->get();
                        return response()->json(['invigilation_types' => $invigilation_types]);
                    }
                }


                $centers = $centers->orderBy('center_no', 'ASC')
                    ->get();
                return response()->json(['centers' => $centers]);
            }

            if ($request->has('invigilations_total')) {


                $invigilation_types =  DB::table('invigilation_types')
                    ->select(
                        'invigilation_types.id',
                        DB::raw("concat(invigilation_types.name,'_',invigilation_catergories.name) as name"),
                    )
                    ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
                    ->where('invigilation_catergories_id', '=', $catergory)
                    ->get();


                $max_subject_sql = "(SELECT MAX(subject_number)  FROM
                                (select cc.center_no,cc.level,cc.session,cc.financial_year, count(0) AS subject_number from candidate_subject
                                join center_candidate cc on cc.candidate_no = candidate_subject.candidate_no and
                                cc.session = candidate_subject.session and cc.financial_year = candidate_subject.financial_year
                                where  cc.session = '$session'   and  cc.financial_year = '$year' and  cc.level = '$level'
                                group by candidate_subject.financial_year,cc.center_no,cc.session,candidate_subject.subject_code
                                order by candidate_subject.financial_year,cc.center_no,candidate_subject.subject_code)
                                            center_invigilation
                                            where center_invigilation.financial_year = center_candidate.financial_year AND
                                            center_invigilation.session = center_candidate.session AND
                                        center_invigilation.level =  center_candidate.level  AND
                                        center_invigilation.center_no = center_candidate.center_no
                                            )";
                $invigilators_sql = "SELECT group_concat(DISTINCT concat(invigilation_types.id,'-', invigilation_roles.invigilator_number) order by invigilation_catergories.name separator ',')
                                            from invigilation_candidates
                                            LEFT JOIN invigilation_roles on invigilation_roles.invigilation_candidate_id= invigilation_candidates.id
                                            INNER JOIN invigilation_types on  invigilation_types.id=invigilation_roles.invigilation_type_id
                                            INNER JOIN invigilation_catergories on  invigilation_catergories.id= invigilation_types.invigilation_catergories_id
                                            WHERE invigilation_candidates.range_start <= ($max_subject_sql)
                                            AND invigilation_candidates.range_end >=($max_subject_sql)
                                            AND invigilation_catergories.id=$catergory";



                $total_invigilators = DB::table('center_candidate')
                    ->select(
                        'center_candidate.center_no',
                        'centers.center_name',
                        'centers.district',
                        DB::raw("$max_subject_sql as candidates"),
                        DB::raw("group_concat(DISTINCT concat(center_candidate.level) order by center_candidate.level separator ',') as levels"),
                        DB::raw("group_concat(DISTINCT concat(center_candidate.session) order by center_candidate.session
                                separator ',') as sessions"),
                        DB::raw("( $invigilators_sql ) as invigilators"),

                    )

                    ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no');

                if (!empty($level)) {
                    $total_invigilators = $total_invigilators->where('center_candidate.level', '=', $level);
                }
                if (!empty($sponsor)) {
                    $total_invigilators = $total_invigilators->where('center_candidate.sponser', '=',  $sponsor);
                }

                if (!empty($session)) {
                    $total_invigilators = $total_invigilators->where('center_candidate.session', '=',  $session);
                }

                if (!empty($year)) {
                    $total_invigilators = $total_invigilators->where('center_candidate.financial_year', '=', $year);
                }

                if (!empty($center_no)) {
                    $total_invigilators = $total_invigilators->where('center_candidate.center_no', '=', $center_no);
                }



                $total_invigilators = $total_invigilators->groupBy('center_candidate.center_no')
                    ->get();

                $datatables = DataTables::of($total_invigilators);
                foreach ($invigilation_types  as $invigilation_type) {

                    $datatables = $datatables->addColumn(str_replace(' ', '_', $invigilation_type->name), function ($candidate) use ($invigilation_type) {
                        $chunks = array_chunk(preg_split('/[-,]/',  $candidate->invigilators), 2);
                        $result = array_combine(array_column($chunks, 0), array_column($chunks, 1));
                        $count = 0;
                        if (isset($result[$invigilation_type->id])) {
                            $count = $result[$invigilation_type->id];
                        } else {
                            $count = 0;
                        }
                        return   $count;
                    });
                }

                $datatables = $datatables->addColumn('total', function ($candidate) use ($invigilation_types) {
                    $total = 0;
                    foreach ($invigilation_types  as $invigilation_type) {
                        $chunks = array_chunk(preg_split('/[-,]/',  $candidate->invigilators), 2);
                        $result = array_combine(array_column($chunks, 0), array_column($chunks, 1));
                        $count = 0;
                        if (isset($result[$invigilation_type->id])) {
                            $count = $result[$invigilation_type->id];
                            $total += $count;
                        } else {
                            $count = 0;
                        }
                    }
                    return   $total;
                });




                return  $datatables->make(true);
            }
            $invigilations = InvigilatorProfile::with('invigilation_role.invigilation_type', 'invigilation_status');
            if (!empty($status)) {
                $invigilations = $invigilations->where('invigilator_profile.progress_status_id', '=', $status);
            }

            if (!empty($session)) {
                $invigilations = $invigilations->where('invigilator_profile.session', '=',  $session);
            }

            if (!empty($year)) {
                $invigilations = $invigilations->where('invigilator_profile.financial_year', '=', $year);
            }

            if (!empty($center_no)) {
                $invigilations = $invigilations->where('invigilator_profile.center_no', '=', $center_no);
            }
            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {

                    $status = $row->invigilation_status;

                    return "<div class='status-tag auxiliar-low highlight auxiliar-low' style='background-color:RGBA($status->color_red, $status->color_green, $status->color_blue, .7);'>

                        </div>";
                })
                ->addColumn('action', function ($row) {
                    $status = $row->invigilation_status;

                    $btn = '
                    <a href="' . route('admin.invigilations.contracts.exportSinglePdf', $row->id)  . '" data-toggle="tooltip"  data-url="" data-original-title="Edit" class="edit-pdf btn btn-success btn-sm fa fa-download"></a>
                    <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.contracts.edit', $row->id)  . '" data-original-title="Edit" class="edit-center btn btn-primary btn-sm fa fa-edit"></a>
                    <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.contracts.destroy', $row->id)   . '" data-original-title="Delete" class="delete-center btn btn-danger btn-sm fa fa-trash"></a>';
                    return  $btn;
                })

                ->rawColumns(['action', 'candidate_range', 'status'])
                ->make(true);
        }

        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $levels =  CenterCandidate::select(DB::raw('level as level'))
            ->orderBy('level', 'DESC')
            ->distinct()
            ->get();
        $sponsors = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->distinct()
            ->orderBy('sponser')
            ->get();
        $centers = Center::get();
        $invigilator_experiences = InvigilatorExperience::get();
        $invigilation_status = InvigilationStatus::get();

        $catergories = InvigilationCatergories::get();

        $invigilation_types = DB::table('invigilation_candidates')
            ->select(
                'invigilation_candidates.range_start',
                'invigilation_candidates.range_end',
                'invigilation_types.name',
                DB::raw("invigilation_catergories.name as catergory_name"),
                'invigilation_roles.id',
                'invigilation_roles.invigilator_number'
            )
            ->leftJoin('invigilation_roles', 'invigilation_roles.invigilation_candidate_id', '=', 'invigilation_candidates.id')
            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
            ->where('invigilation_catergories.id', '=', 2)
            ->get();
        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');

        $types = DB::table('center_candidate')->select(
            [
                'type'
            ],
        )->distinct()
            ->orderBy('type')
            ->get();


        $sessions = Session::where('financial_year', $years[0])->get();


        $acceptedNumber = DB::table('invigilator_profile')
            ->select(
                DB::raw("COUNT( progress_status_id) as acceptedNumber"),
            )
            ->where('progress_status_id', '=', 2)
            ->where('financial_year', $years[0])
            ->count();
        $pendingNumber = DB::table('invigilator_profile')
            ->select(
                DB::raw("COUNT( progress_status_id) as pendingNumber"),
            )
            ->where('financial_year', $years[0])
            ->where('progress_status_id', '=', 1)
            ->count();
        $declinedNumber = DB::table('invigilator_profile')
            ->select(
                DB::raw("COUNT( progress_status_id) as declinedNumber"),
            )
            ->where('financial_year', $years[0])
            ->where('progress_status_id', '=', 3)
            ->count();
        return view('admin.invigilation.contracts.index', compact(
            'acceptedNumber',
            'declinedNumber',
            'pendingNumber',
            'sponsors',
            'catergories',
            'invigilator_experiences',
            'invigilation_status',
            'levels',
            'invigilation_types',
            'sessions',
            'centers',
            'types',
            'years',
            'invigilation_types',
            'sponsors'
        ));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $validator = Validator::make($request->all(), [
            'invigilation_role_id' => ['required', Rule::notIn($this->validate_invigilators($request->center_no, $request->category_id))],
            'national_id' => ['required', 'regex:/^(\d{11}|\d{12}|\d{13})$/', Rule::unique('invigilator_profile')->where(function ($q) use ( $financial_year) {
                return $q->where('financial_year',   $financial_year);
            }),],
            'surname' => 'required|string',
            'other_names' => 'required|string',
            'email' => ['required', 'string', 'email', Rule::unique('invigilator_profile')->where(function ($q) use ( $financial_year) {
                return $q->where('financial_year',  $financial_year);
            })],
            'phone_number' => 'required',
            'experience_id' => 'required|string',
            'principal_declare' =>  'required|string',
        ], ['invigilation_role_id.not_in' => 'Reached the limit']);



        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $center_no = $request->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);


        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)
            ->where('is_active', '=', 1)
            ->first();

        $token = md5(uniqid());

        $status = InvigilationStatus::where('status', '=', 1)->orderBy('order_status', 'ASC')->first();


         InvigilatorProfile::create([
            'invigilation_role_id' => $request->invigilation_role_id,
            'center_no' => $request->center_no,
            'national_id' => $request->national_id,
            'surname' => $request->surname,
            'other_names' => $request->other_names,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'experience_id' => $request->experience_id,
            'accessibility' => $request->has('accessibility') ? 1 : 0,
            'integrity' => $request->has('integrity') ? 1 : 0,
            'workshop' => $request->has('workshop') ? 1 : 0,
            'principal_declare' => $request->principal_declare,
            'token' => $token,
            'progress_status_id' => $status->id,
            'session' => $session->session,
            'financial_year' => $financial_year,
        ],);


        // send email
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $url = route('applications.index', $token);
        $declined = route('applications.index', ['token' => $token, 'declined' => 1]);
        $mailheader = '\assets\images\mailheader.jpg';
        $data = ['center_no' => $center_no, 'other_names' => $request->other_names, 'surname' => $request->surname, 'url' => $url, 'declined' => $declined, 'center_name' => $center->center_name, 'mailheader' => $mailheader];
        Mail::to($request->email)->queue(new InvigilatorMail($data));
        return response()->json(['success' => 'Invigilator add successfully']);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invigilation = InvigilatorProfile::with('invigilation_role.invigilation_type.invigilation_catergories')->find($id);

        $url = route('admin.invigilations.contracts.update', $id);
        $catergory = $invigilation->invigilation_role->invigilation_type->invigilation_catergories;

        return response()->json(['invigilation' => $invigilation, 'url' => $url, 'catergory' => $catergory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'invigilation_role_id' => ['required', Rule::notIn($this->validate_invigilators($request->center_no, $request->catergory, $id))],
            'center_no' => 'required|string',
            'national_id' => ['required', 'regex:/^(\d{11}|\d{12}|\d{13})$/', 'unique:invigilator_profile,national_id,' . $id],
            'surname' => 'required|string',
            'other_names' => 'required|string',
            'email' => 'required|string|email|unique:invigilator_profile,email,' . $id,
            'phone_number' => 'required|string',
            'experience_id' => 'required|string',
            'principal_declare' =>  'required|string',
            'progress_status_id' =>  'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' =>  $validator->errors()]);
        } else {

            $token = md5(uniqid());
            $invigilation = InvigilatorProfile::find($id);
            $invigilation->invigilation_role_id = $request->invigilation_role_id;
            $invigilation->national_id = $request->national_id;
            $invigilation->surname = $request->surname;
            $invigilation->other_names = $request->other_names;
            $invigilation->email = $request->email;
            $invigilation->phone_number = $request->phone_number;
            $invigilation->experience_id = $request->experience_id;
            $invigilation->accessibility = $request->has('accessibility') ? 1 : 0;
            $invigilation->integrity = $request->has('integrity') ? 1 : 0;
            $invigilation->workshop = $request->has('workshop') ? 1 : 0;
            $invigilation->principal_declare = $request->principal_declare;
            $invigilation->progress_status_id = $request->progress_status_id;
            if ($request->has('resend_token')) {
                $invigilation->token = $token;
            }

            $invigilation->save();
            if ($request->has('resend_token')) {

                // send email
                $center_no = $request->center_no;
                $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
                $declined = route('applications.declined', ['token' => $token, 'declined' => 1]);
                $url = route('applications.index', $token);
                $mailheader = '\assets\images\mailheader.jpg';
                $data = ['center_no' => $center_no, 'other_names' => $request->other_names, 'surname' => $request->surname, 'url' => $url, 'declined' => $declined, 'center_name' => $center->center_name, 'mailheader' => $mailheader];
                Mail::to($request->email)->queue(new InvigilatorMail($data));
            }
        }

        return response()->json(['success' => 'Invigilator update successfully']);
    }
    public function invigilationReport(Request $request)
    {
        $level = empty($request->level) ? 'LGCSE' : $request->level;
        $session = empty($request->session) ? 'November' : $request->session;
        $year = empty($request->year) ? '2024-2025' : $request->year;
        $catergory = empty($request->catergory) ? '1' : $request->catergory;
        $center = $request->center_no;
        $district = $request->district_id;
        $status = $request->status;
        switch ($request->file_type) {
            case '3':
                # switch to download pdf...

                ini_set('memory_limit', '-1');
                set_time_limit(-1);
                $fileName = "Invigilator Reports " . time() . '.csv';
                $headers = array(
                    "Content-type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$fileName",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                );



                $invigilation_types =  DB::table('invigilation_types')
                    ->select(
                        'invigilation_types.id',
                        DB::raw("concat(invigilation_types.name,'-',invigilation_catergories.name) as name"),
                    )
                    ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
                    ->where('invigilation_catergories_id', '=', $catergory)
                    ->get();


                $types_headers = "Centre Number,Centre Name,#.Candidates,";
                foreach ($invigilation_types->pluck('name')->toArray() as $type) {
                    $types_headers .= $type;
                    if (!next($invigilation_types)) {
                        $types_headers  .= ",";
                    }
                }
                $columns = explode(',', $types_headers);
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);


                $max_subject_sql = "(SELECT MAX(subject_number)  FROM
                                (select cc.center_no,cc.level,cc.session,cc.financial_year, count(0) AS subject_number from candidate_subject
                                join center_candidate cc on cc.candidate_no = candidate_subject.candidate_no and
                                cc.session = candidate_subject.session and cc.financial_year = candidate_subject.financial_year
                                where  cc.session ='$session'   and  cc.financial_year ='$year' and  cc.level='$level'
                                group by candidate_subject.financial_year,cc.center_no,cc.session,candidate_subject.subject_code
                                order by candidate_subject.financial_year,cc.center_no,candidate_subject.subject_code)
                                            center_invigilation
                                            where center_invigilation.financial_year = center_candidate.financial_year AND
                                            center_invigilation.session = center_candidate.session AND
                                        center_invigilation.level =  center_candidate.level  AND
                                        center_invigilation.center_no = center_candidate.center_no
                                            )";
                $invigilators_sql = "SELECT group_concat(DISTINCT concat(invigilation_types.id,'-', invigilation_roles.invigilator_number) order by invigilation_catergories.name separator ',')
                                            from invigilation_candidates
                                            LEFT JOIN invigilation_roles on invigilation_roles.invigilation_candidate_id= invigilation_candidates.id
                                            INNER JOIN invigilation_types on  invigilation_types.id=invigilation_roles.invigilation_type_id
                                            INNER JOIN invigilation_catergories on  invigilation_catergories.id= invigilation_types.invigilation_catergories_id
                                            WHERE invigilation_candidates.range_start <= ($max_subject_sql)
                                            AND invigilation_candidates.range_end >=($max_subject_sql)
                                            AND invigilation_catergories.id=$catergory";

                DB::table('center_candidate')
                    ->select(
                        'center_candidate.center_no',
                        'centers.center_name',
                        'centers.district',
                        DB::raw("$max_subject_sql as candidates"),
                        DB::raw("group_concat(DISTINCT concat(center_candidate.level) order by center_candidate.level separator ',') as levels"),
                        DB::raw("group_concat(DISTINCT concat(center_candidate.session) order by center_candidate.session
                                separator ',') as sessions"),
                        DB::raw("( $invigilators_sql ) as invigilators"),

                    )
                    ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no')
                    ->where('center_candidate.level', '=', $level)
                    ->where('center_candidate.session', '=', $session)
                    ->where('center_candidate.financial_year', '=', $year)
                    ->groupBy('center_candidate.center_no')
                    ->orderBy('center_candidate.center_no', "ASC")
                    ->each(function (object $candidate) use (
                        $invigilation_types,
                        &$file,
                    ) {
                        $chunks = array_chunk(preg_split('/[-,]/',  $candidate->invigilators), 2);


                        $result = array_combine(array_column($chunks, 0), array_column($chunks, 1));


                        $center_results = "$candidate->center_no,$candidate->center_name,$candidate->candidates,";
                        foreach ($invigilation_types  as $invigilation_type) {
                            if (isset($result[$invigilation_type->id])) {
                                $center_results .= $result[$invigilation_type->id];
                            } else {
                                $center_results .= '0';
                            }
                            if (!next($invigilation_types)) {
                                $center_results .= ",";
                            }
                        }

                        fputcsv($file, explode(",", $center_results));
                    });
                fclose($file);

                return Response::make('', 200, $headers);

                break;
            case '2':

                // variable contains requests

                // Set query
                $invigilation_contracts = InvigilatorProfile::with('invigilation_role.invigilation_type', 'invigilation_role.invigilator_paymentamount', 'invigilator_district', 'invigilator_payment', 'invigilation_status');


                if (!empty($center)) {
                    $invigilation_contracts = $invigilation_contracts->where('center_no', '=', $center);
                }
                if (!empty($session)) {
                    $invigilation_contracts = $invigilation_contracts->where('session', '=', $session);
                }
                if (!empty($year)) {
                    $invigilation_contracts = $invigilation_contracts->where('financial_year', '=', $year);
                }
                if (!empty($district)) {
                    $invigilation_contracts = $invigilation_contracts->where('district_id', '=', $district);
                }

                $status = InvigilationStatus::where('status', '!=', 0)
                    ->orderBy('order_status', 'ASC')->first();
                $invigilation_contracts = $invigilation_contracts->where('progress_status_id', '!=', $status->id)
                    ->get();




                $fpdi = new exFPDF();
                $fpdi->SetTopMargin(78);
                $fontSize = 11;
                $fpdi->SetFont('Helvetica', '', $fontSize);
                $width = $fpdi->GetPageWidth() - 10; // Width of Current Page
                $height = $fpdi->GetPageHeight() - 10; // Height of Current Page
                //$file = public_path('assets/pdf/Filled_Contract_Form.pdf');
                $file = "/home/ecol/ecol.coltech.co.za/assets/pdf/Filled_Contract_Form.pdf";

                // Get data from database

                foreach ($invigilation_contracts as $invigilation_contracts) {
                    // Get data from database
                    $national_id = $invigilation_contracts->national_id;

                    $invigilation_role_id = $invigilation_contracts->invigilation_role->invigilation_type->name;

                    $full_names = $invigilation_contracts->other_names . "  " . $invigilation_contracts->surname;

                    //$district_id = $invigilation_contracts->invigilator_district->district_name;

                    $village = $invigilation_contracts->village;

                    $phone_number = $invigilation_contracts->phone_number;

                    $payment_id = $invigilation_contracts->invigilator_payment->name;

                    $branch = $invigilation_contracts->branch;

                    $bank_name = $invigilation_contracts->bank_name;

                    $account_number = $invigilation_contracts->account_number;

                    $payable_phone_number = $invigilation_contracts->payable_phone_number;

                    $center_no = $invigilation_contracts->center_no;

                    $updated_at = $invigilation_contracts->updated_at;

                    $issessionbased = $invigilation_contracts->invigilation_role->is_sessions;
                    if ($issessionbased == '1') {
                        $issessionbased = "per session";
                    } else {
                        $issessionbased = " ";
                    }
                    $amount = number_format($invigilation_contracts->invigilation_role->invigilator_paymentamount->amount, 2, '.', '') . ' ' . $issessionbased;

                    $updated_at = $invigilation_contracts->updated_at;

                    if (($invigilation_contracts->invigilation_status->name) == 'Accepted') {
                        $fpdi->AddPage();
                        $fpdi->setSourceFile($file);
                        $tplIdx = $fpdi->importPage(1);
                        $size = $fpdi->getTemplateSize($tplIdx);

                        $fpdi->useTemplate($tplIdx, null, null, $size['width'], $size['height'], true);
                        $fpdi->SetFont('Arial');

                        //+++++++++++++++++++ map data into existing pdf +++++++++++++++++++
                        //  national id
                        $fpdi->SetXY(132.6,  64.5);

                        $invigilator = array(
                            'invigilator' =>   $invigilation_contracts->id,
                            'national_id' =>   $national_id,
                            'full_names' =>  $full_names,
                            'center_no ' => $center_no,
                        );

                        $decodedImg = grCodeGenerator($national_id, $invigilator);
                        $pic = 'data://text/plain;base64,' .  $decodedImg;

                        $fpdi->Image($pic, 170, 38, 32, 29, 'png');




                        $fpdi->Cell($width * 2 / 4, 6,  $national_id, 0);

                        // invigilation id
                        $fpdi->SetXY(148.1,  80.2);
                        $fpdi->Cell($width * 2 / 4, 6, $invigilation_role_id, 0);

                        // other names
                        $fpdi->SetXY(13,  64.5);
                        $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                        // district
                        $fpdi->SetXY(121.4,  72.3);
                        //$fpdi->Cell($width * 2 / 4, 6, $district_id, 0);

                        // Other village
                        $fpdi->SetXY(25.7,  72.3);
                        $fpdi->Cell($width * 2 / 4, 6, $village, 0);

                        // center
                        $fpdi->SetXY(138.8,  155.5);
                        $fpdi->Cell($width * 2 / 4, 6, $center_no, 0);
                        // phone number:
                        $fpdi->SetXY(84.0,  212.9);
                        $fpdi->Cell($width * 2 / 4, 6,  $amount, 0);

                        // payemnt method
                        $fpdi->SetXY(47.7,  220.9);
                        $fpdi->Cell($width * 2 / 4, 6, $payment_id, 0);

                        // Branch code
                        $fpdi->SetXY(148.6,  236.7);
                        $fpdi->Cell($width * 2 / 4, 6, $branch, 0);

                        // other names
                        $fpdi->SetXY(18.3,  155.5);
                        $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                        // Bank name
                        $fpdi->SetXY(32.0,  228.75);
                        $fpdi->Cell($width * 2 / 4, 6, $bank_name, 0);


                        // account number
                        $fpdi->SetXY(43.1,  236.7);
                        $fpdi->Cell($width * 2 / 4, 6,  $account_number, 0);

                        // payable number
                        $fpdi->SetXY(47,  244.6);
                        $fpdi->Cell($width * 2 / 4, 6, $payable_phone_number, 0);

                        // year
                        $fpdi->SetXY(100,  271);
                        $fpdi->Cell($width * 2 / 4, 6,  $updated_at, 0);

                        // signed
                        $fpdi->SetXY(41,  271);
                        $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);
                    } else {
                        continue;
                    }
                }

                $fpdi->Output('Contracts' . '.pdf', 'F');
                header('Content-type: application/pdf');
                header('Content-disposition: attectment; filename = Contracts' . '.pdf');
                readfile('Contracts' . '.pdf');
                unlink('Contracts' . '.pdf');



                exit();

                break;
            case '1':
                $fileName = "Sponser Reports $year" . time() . '.csv';
                $headers = array(
                    "Content-type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$fileName",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                );


                // variable contains requests

                // Set query
                $invigilation_contracts = InvigilatorProfile::with(
                    'invigilation_role.invigilation_type',
                    'invigilation_role.invigilator_paymentamount',
                    'invigilator_district',
                    'invigilator_payment',
                    'invigilation_status',
                    'timesheet'
                );



                if (!empty($center)) {
                    $invigilation_contracts = $invigilation_contracts->where('center_no', '=', $center);
                }
                if (!empty($session)) {
                    $invigilation_contracts = $invigilation_contracts->where('session', '=', $session);
                }
                if (!empty($year)) {
                    $invigilation_contracts = $invigilation_contracts->where('financial_year', '=', $year);
                }
                if (!empty($district)) {
                    $invigilation_contracts = $invigilation_contracts->where('district_id', '=', $district);
                }
                if (!empty($request->catergory)) {
                    //invigilation_catergories_id
                    $invigilation_contracts = $invigilation_contracts->whereHas('invigilation_role.invigilation_type', function ($q) use ($request) {
                        $q->where('invigilation_catergories_id', '=', $request->catergory);
                    });
                }


                if (!empty($status)) {
                    $invigilation_contracts = $invigilation_contracts->where('progress_status_id', '=', $status);
                }

                $columns = array('CENTRE NUMBER', 'NATIONAL ID', 'SURNAME', 'OTHER NAME', 'GENDER', 'PHONE NUMBER', 'ROLE', 'STATUS', 'REQUIERD SESSIONS', 'AMOUNT', 'TOTAL SESSIONS', 'TOTAL AMOUNT', 'PAYMENT METHOD', 'BANK NAME', 'BANK BRANCH', 'ACCOUNT NUMBER', 'MOBILE NUMBER', 'TILL NUMBER');
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                $invigilation_contracts = $invigilation_contracts->each(function (object $invigilator) use (
                    &$file,
                ) {
                    $is_sessions = $invigilator->invigilation_role->is_sessions;
                    $amount = $invigilator->invigilation_role->invigilator_paymentamount->amount;
                    $total_session = 0;
                    $total_amount = 0;
                    $session_required = 'NO';

                    if (count($invigilator->timesheet) >= 1 || $is_sessions == 1) {
                        $total_amount = $amount * count($invigilator->timesheet);
                        $session_required = "YES";
                        $total_session = count($invigilator->timesheet);
                    } else {
                        $total_amount = $amount;
                    }


                    fputcsv($file, array(
                        $invigilator->center_no,
                        $invigilator->national_id,
                        strtoupper($invigilator->surname),
                        strtoupper($invigilator->other_names),
                        strtoupper($invigilator->gender),
                        $invigilator->phone_number,
                        strtoupper($invigilator->invigilation_role->invigilation_type->name . "-" . $invigilator->invigilation_role->invigilation_type->invigilation_catergories->name),
                        strtoupper($invigilator->invigilation_status->name),
                        $session_required,
                        $amount,
                        $total_session,
                        $total_amount,
                        isset($invigilator->invigilator_payment->name) ? $invigilator->invigilator_payment->name : ' ',
                        $invigilator->bank_name,
                        $invigilator->branch,
                        $invigilator->account_number,
                        $invigilator->payable_phone_number,
                        $invigilator->tin_number,
                    ));
                });

                fclose($file);
                if ($request->has('send')) {
                    $csv_string = ob_get_contents();
                    ob_end_clean();
                    $validator = Validator::make($request->all(), [
                        'subject' => 'required|string',
                        'email_to' => 'required|string|email',
                        'body' => 'required|string',
                    ]);
                    if ($validator->fails()) {
                        return response()->json(['errors' =>  $validator->errors()]);
                    }
                    $from = Auth::user()->email;
                    $subject = "November 2024 Invigilators Payment";
                    $email = $request->email_to;
                    $body = "Good day Doc., </br></br> Please find the attached invigilators list. I $from humbly request your kind office for their payment approval.
                          </br>

                            Thank you</br>

Mrs. Kananelo Mohasi</br>
Human Resources Manager, Examinations Council of Lesotho</br>

T: +266 22312880 | T: +266 52300116 | M: +266 63921010</br>
E: mohasik@examscouncil.org.ls | W: www.examscouncil.org.ls</br>
50 Constitution Road, Maseru 100, Lesotho</br>";

                    $mail = new PHPMailer(true);
                    try {
                        //Server settings
                        // $mail->SMTPDebug = 1;                      // Enable verbose debug output
                        $mail->isSMTP();                                            // Send using SMTP
                        $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                        $mail->Host = "smtp.gmail.com";
                        // Set the SMTP server to send through
                        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
                        $mail->SMTPKeepAlive = true;
                        //  $mail->Mailer = “smtp”;
                        $mail->Username   = 'noreply@ecol.org.ls';                     // SMTP username
                        $mail->Password   = 'Ec0l.OTP2024@wCiRaPNn7%}^9w5-';                               // SMTP password
                        $mail->Port = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above


                        //Recipients
                        $mail->setFrom($from, 'Examinations Council of Lesotho');
                        // sender
                        $mail->addAddress($email);
                        $mail->addStringAttachment($csv_string, "Invigilators List.csv");
                        // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject =  $subject;
                        $mail->Body    = $body;
                        $mail->AltBody = strip_tags($body);
                        if ($mail->send()) {
                            return response()->json(['success' => "Successully  send list to   $email"]);
                        } else {
                            return response()->json(['fail' => "Failed"]);
                        }
                    } catch (Exception $e) {
                        return response()->json(['errors' => 1]);
                    }
                }
                return Response::make('', 200, $headers);
                break;
            default:
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function exportSinglePdf(string $id)
    {

        $invigilation_contracts = InvigilatorProfile::with('invigilation_role', 'invigilation_role.invigilation_type', 'invigilation_role.invigilator_paymentamount', 'invigilator_district', 'invigilator_payment', 'invigilation_status')->find($id);
        if (($invigilation_contracts->invigilation_status->name) == 'Accepted') {
            if (!empty($invigilation_contracts)) {
                // if has value then return
                $fpdi = new exFPDF();
                $fpdi->SetTopMargin(78);
                $fontSize = 11;
                $fpdi->SetFont('Helvetica', '',  $fontSize);
                $width = $fpdi->GetPageWidth() - 10;  // Width of Current Page
                $height = $fpdi->GetPageHeight() - 10; // Height of Current Page
                //$file = public_path("assets/pdf/Filled_Contract_Form.pdf");
                $file = "/home/ecol/ecol.coltech.co.za/assets/pdf/Filled_Contract_Form.pdf";

                // Get data from database
                $national_id = $invigilation_contracts->national_id;

                $invigilation_role_id = $invigilation_contracts->invigilation_role->invigilation_type->name;
                $full_names = $invigilation_contracts->other_names . "  " . $invigilation_contracts->surname;
                $district_id = $invigilation_contracts->invigilator_district->district_name;
                $village = $invigilation_contracts->village;
                $payment_id = $invigilation_contracts->invigilator_payment->name;
                $branch = $invigilation_contracts->branch;
                $bank_name = $invigilation_contracts->bank_name;
                $account_number = $invigilation_contracts->account_number;
                $payable_phone_number = $invigilation_contracts->payable_phone_number;
                $center_no = $invigilation_contracts->center_no;
                $updated_at = $invigilation_contracts->updated_at;
                $issessionbased = $invigilation_contracts->invigilation_role->is_sessions;
                if ($issessionbased == '1') {
                    $issessionbased = "per session";
                } else {
                    $issessionbased = " ";
                }
                $amount = number_format($invigilation_contracts->invigilation_role->invigilator_paymentamount->amount, 2, '.', '') . ' ' . $issessionbased;

                $updated_at = $invigilation_contracts->updated_at;



                $fpdi->AddPage();
                $fpdi->setSourceFile($file);
                $tplIdx = $fpdi->importPage(1);
                $size = $fpdi->getTemplateSize($tplIdx);


                $fpdi->useTemplate($tplIdx, null, null, $size['width'], $size['height'], true);
                $fpdi->SetFont('Arial');

                //+++++++++++++++++++ map data into existing pdf +++++++++++++++++++
                //  national id
                $fpdi->SetXY(132.6,  64.5);
                $fpdi->Cell($width * 2 / 4, 6,  $national_id, 0);


                $invigilator = array(
                    'invigilator' =>   $invigilation_contracts->id,
                    'national_id' =>   $national_id,
                    'full_names' =>  $full_names,
                    'center_no ' => $center_no,
                );

                $decodedImg = grCodeGenerator($national_id, $invigilator);
                $pic = 'data://text/plain;base64,' .  $decodedImg;

                $fpdi->Image($pic, 170, 38, 32, 29, 'png');

                // invigilation id
                $fpdi->SetXY(148.1,  80.2);
                $fpdi->Cell($width * 2 / 4, 6, $invigilation_role_id, 0);

                // other names
                $fpdi->SetXY(13,  64.5);
                $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                // district
                $fpdi->SetXY(121.4,  72.3);
                $fpdi->Cell($width * 2 / 4, 6, $district_id, 0);

                // Other village
                $fpdi->SetXY(25.7,  72.3);
                $fpdi->Cell($width * 2 / 4, 6, $village, 0);

                // center
                $fpdi->SetXY(138.8,  155.5);
                $fpdi->Cell($width * 2 / 4, 6, $center_no, 0);
                // phone number:
                $fpdi->SetXY(84.0,  212.9);
                $fpdi->Cell($width * 2 / 4, 6,  $amount, 0);

                // payemnt method
                $fpdi->SetXY(47.7,  220.9);
                $fpdi->Cell($width * 2 / 4, 6, $payment_id, 0);

                // Branch code
                $fpdi->SetXY(148.6,  236.7);
                $fpdi->Cell($width * 2 / 4, 6, $branch, 0);

                // other names
                $fpdi->SetXY(18.3,  155.5);
                $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                // Bank name
                $fpdi->SetXY(32.0,  228.75);
                $fpdi->Cell($width * 2 / 4, 6, $bank_name, 0);


                // account number
                $fpdi->SetXY(43.1,  236.7);
                $fpdi->Cell($width * 2 / 4, 6,  $account_number, 0);

                // payable number
                $fpdi->SetXY(47,  244.6);
                $fpdi->Cell($width * 2 / 4, 6, $payable_phone_number, 0);

                // year
                $fpdi->SetXY(100,  271);
                $fpdi->Cell($width * 2 / 4, 6,  $updated_at, 0);


                // signed
                $fpdi->SetXY(41,  271);
                $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                $fpdi->Output("Invigilation Contract"  . ".pdf", "F");
                header("Content-type: application/pdf");
                header("Content-disposition: attectment; filename = Invigilation Contract" . '.pdf');
                readfile("Invigilation Contract" . ".pdf");
                unlink("Invigilation Contract" . '.pdf');
                exit;
            } else {
                return redirect('pages/404')->with('error', "Contract Printing failed");
            }
        } else {
            return response()->json(['error' => 'Contract is not signed or it is declined.']);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        InvigilatorProfile::find($id)->delete();
        return response()->json(['success' => 'Invigilator deleted successfully.']);
    }

    private function validate_invigilators($center_no, $type, $id = null)
    {
        if (empty($center_no) || empty($type)) {
            return  [];
        }
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)
            ->where('is_active', '=', 1)
            ->first();

        $totalcandidates = Payment::highestSubject($center_no, $center->level,  $session->session, $session->financial_year);;

        $status = InvigilationStatus::where('status', '=', 0)
            ->orderBy('order_status', 'ASC')->first();



        $invigilation_types = DB::table('invigilation_candidates')
            ->select(
                'invigilation_candidates.range_start',
                'invigilation_candidates.range_end',
                'invigilation_types.name',
                DB::raw("invigilation_catergories.name as catergory_name"),
                'invigilation_roles.id',
                'invigilation_roles.invigilator_number'
            )
            ->leftJoin('invigilation_roles', 'invigilation_roles.invigilation_candidate_id', '=', 'invigilation_candidates.id')
            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
            ->where('invigilation_catergories.id', '=', $type)
            ->where('invigilation_candidates.range_start', '<=', $totalcandidates)
            ->where('invigilation_candidates.range_end', '>=', $totalcandidates)
            ->get();




        $invigilation_roles = DB::table('invigilator_profile')
            ->select('invigilation_role_id', DB::raw("COUNT(invigilation_role_id) as invigilation_number"))
            ->groupBy('center_no', 'invigilation_role_id');

        if (!empty($id)) {
            $id_role = InvigilatorProfile::find($id);
            $invigilation_roles = $invigilation_roles->where('invigilation_role_id', '<>', $id_role->invigilation_role_id);
        }
        $invigilation_roles = $invigilation_roles->where('center_no', '=', $center_no)
            ->where('progress_status_id', '!=', $status->id)
            ->get();

        $rules = [];
        foreach ($invigilation_types as    $invigilation_type) {
            foreach ($invigilation_roles as $invigilation_role) {
                if ($invigilation_type->id == $invigilation_role->invigilation_role_id && $invigilation_type->invigilator_number <= $invigilation_role->invigilation_number) {
                    array_push($rules, $invigilation_role->invigilation_role_id);
                }
            }
        }
        return    $rules;
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
}
