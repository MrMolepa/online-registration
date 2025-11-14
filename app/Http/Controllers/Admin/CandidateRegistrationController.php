<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Candidate;
use App\Models\CandidateArrangement;
use App\Models\CandidateUser;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Guardian;
use App\Models\GuardianType;
use App\Models\Invoice;
use App\Models\Level;
use App\Models\OptionHeader;
use App\Models\Session;
use App\Models\SpecialNeed;
use App\Models\Subject;
use App\Models\SubjectCandidate;
use App\Rules\CheckDupsSubject;
use App\Rules\Extended;
use App\Rules\SubjectsGrouping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CandidateRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
        $sponsors = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->distinct()
            ->orderBy('sponser')
            ->get();
        $types = DB::table('center_candidate')->select(
            [
                'type'
            ],
        )->distinct()
            ->orderBy('type')
            ->get();
        $centers  = Center::get();
        if ($request->ajax()) {
            $candidates = DB::table('candidate_subject')
                ->select(
                    [
                        'center_candidate.id',
                        'center_candidate.center_no',
                        'center_candidate.candidate_no',
                        'center_candidate.national_id',
                        'center_candidate.session',
                        'center_candidate.level',
                        'center_candidate.type',
                        'center_candidate.subject_number',
                        'candidates.candidate_surname',
                        'candidates.candidate_other_name',
                        'candidates.date_of_birth',
                        'candidates.gender',
                        'center_candidate.sponser',
                        DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
                   order by candidate_subject.subject_code separator ',') as subjects")
                    ],
                )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                ->join('center_candidate', function ($join) {
                    $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                    $join->on('candidate_subject.level', '=', 'center_candidate.level');
                    $join->on('candidate_subject.session', '=', 'center_candidate.session');
                    $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                })->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session');

            if (!is_null($request->year)) {
                $candidates = $candidates->where('center_candidate.financial_year', $request->year);
            }
            if (!is_null($request->center_no)) {
                $candidates =  $candidates->where('center_no', $request->center_no);
            }
            if (!is_null($request->level)) {
                $candidates = $candidates->where('center_candidate.level', '=', $request->level);
            }
            if (!is_null($request->session)) {
                $candidates = $candidates->where('center_candidate.session', '=', $request->session);
            }

            if (!is_null($request->sponsor)) {
                $candidates = $candidates->where('center_candidate.sponser', '=', $request->sponsor);
            }

            if (!is_null($request->type)) {
                $candidates = $candidates->where('center_candidate.type', '=', $request->type);
            }
            if (!is_null($request->subject)) {
                $candidates = $candidates->where('candidate_subject.subject_code', '=', $request->subject);
            }
            $candidates = $candidates->orderBy('center_candidate.candidate_no');
            return DataTables::of($candidates)
                ->editColumn('national_id', function ($model) {
                    return str_pad($model->national_id, 12, '0', STR_PAD_LEFT);
                })
                ->editColumn('candidate_no', function ($model) {
                    return str_pad($model->candidate_no, 9, '0', STR_PAD_LEFT);
                })
                ->editColumn('candidate_other_name', function ($model) {
                    return $model->candidate_other_name;
                })

                ->editColumn('subjects', function ($model) {
                    $output = "";
                    $subjects = explode(",", $model->subjects);
                    foreach ($subjects as $subject) {
                        $output .= ' <span class="subject-data">' . $subject . '</span>';
                    }
                    return $output;
                })
                ->editColumn('actions', function ($model) {
                    $actions = '<div class="btn-group">';

                    $actions .= '<a data-action="' . route('admin.candidate-registation.edit', $model->id) . '"  class="edit-candidate btn btn-sm btn-primary" rel="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                        </a>';

                    $actions   .=  '<a data-action="' . route('admin.candidate-registation.destroy', $model->id) . '" class="delete-candidate  btn btn-sm btn-danger"  type="button" rel="tooltip" title="Delete">
                                        <i class="far fa-trash-alt"></i>
                                </a>';
                    $actions .= '</div>';
                    return    $actions;
                })


                ->rawColumns(['actions', 'subjects', 'candidate_other_name'])

                ->make(true);
        }
        $levels = Level::get();
        $sessions = Session::where('financial_year', $years[0])->get();
        $specialNeeds = SpecialNeed::get();
        $guardian_types =  GuardianType::get();
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        return view('admin.candidates.candidates-registration', compact('centers', 'sponsors', 'years', 'levels', 'types', 'sessions', 'districts', 'specialNeeds', 'guardian_types'));
    }

    public function registerdCandidates(Request  $request)
    {


        $level = $request->level;
        $session = $request->session;
        $center = $request->center;
        $filter = $request->filter;
        $year = $request->year;
        $sponsor = $request->sponsor;
        $type = $request->type;
        $subject = $request->subject;
        $output = "";

        $subjects = DB::table('subjects')
            ->select(
                [
                    'subjects.subject_code',
                    'subjects.subject_name',
                    'subjects.short_name',
                    'levels.level',
                ],
            )
            ->join('levels', 'levels.id', '=', 'subjects.level');

        $candidates_per_centers = DB::table('center_candidate')
            ->select(
                [
                    'center_candidate.center_no',
                    'centers.center_name',
                    'centers.district',
                    DB::raw("count(DISTINCT center_candidate.candidate_no ) as candidates"),
                    DB::raw("group_concat(DISTINCT concat(center_candidate.level)
                order by center_candidate.level separator ',') as levels"),
                    DB::raw("group_concat(DISTINCT concat(center_candidate.session)
                order by center_candidate.session separator ',') as sessions"),
                    DB::raw("group_concat(DISTINCT concat(center_candidate.sponser)
                order by center_candidate.sponser separator ',') as sponsors"),
                ],
            )->join('candidate_subject', 'candidate_subject.candidate_no', '=', 'center_candidate.candidate_no')
            ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no');

        if (!is_null($subject)) {
            $candidates_per_centers = $candidates_per_centers->where('candidate_subject.subject_code', '=', $subject);
            $subjects = $subjects->where('subjects.subject_code', '=',  $subject);
        }
        if (!is_null($level)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.level', '=', $level);
            $subjects = $subjects->where('levels.level', '=', $level);
        }
        if (!is_null($session)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.session', '=', $session);
        }
        if (!is_null($center)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.center_no', '=', $center);
        }
        if (!is_null($year)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.financial_year', '=',  $year);
        }
        if (!is_null($sponsor)) {
            $candidates_per_centers = $candidates_per_centers->whereIn('center_candidate.sponser', ['' .  $sponsor . '']);
        }
        if (!is_null($type)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.type', '=', 'type');
        }


        $candidates_per_centers = $candidates_per_centers->groupBy('center_candidate.center_no')
            ->take($filter)
            ->get();
        $subjects = $subjects->get();

        $totalCandidates = 0;

        if (count($candidates_per_centers) > 0) {
            $output = "<table class='table table-condensed table-striped'>
            <thead>
                <tr>
                    <th>Center No</th>
                    <th>Center Name</th>
                    <th>District</th>
                    <th>Level(s)</th>
                    <th>Session(s)</th>
                    <th>Sponsor(s)</th>
                    <th>Registered Candidates</th>
                </tr>
            </thead>
            <tbody>";
            foreach ($candidates_per_centers as $center) {
                $output .= "<tr>
                        <td>$center->center_no </td>
                        <td>$center->center_name </td>
                        <td>$center->district </td>
                        <td> $center->levels </td>
                        <td> $center->sessions </td>
                        <td> $center->sponsors </td>
                        <td> $center->candidates </td>
                    </tr>";
                $totalCandidates = $totalCandidates + $center->candidates;
            }

            $output  .= "<th colspan=6 class='heading'>Total Candiates</th>
            <th> $totalCandidates</th>";




            $output  .= " </tbody>
        </table>";
        } else {
            $output =  '<div>
                            No Candidates
                        </div>';
        }

        return response()->json(['cendidate_per_center' => $output, 'centers' => $candidates_per_centers, 'subjects' => $subjects, 'filet' => $request->all()]);
    }

    public function exportAmendmentList(Request $request)
    {


        $center = $request->has('center') ? $request->center : 'Amendments List';
        $filename =  $center . ' ' . time();
        $headers = array(
            "Content-type" => "text/plain",
            "Content-Disposition" => "attachment; filename=" . $filename . '.txt',
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );



        if ($request->file_type == "CSV") {
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $filename . '.csv',
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
        }


        $candidates = DB::table('amendments')
            ->select('amendments.*', 'center_candidate.center_no')
            ->join("center_candidate", "center_candidate.candidate_no", "=", "amendments.candidate_no")
            ->whereYear('amendments.amend_date', date('Y'))
            ->orderBy('center_candidate.center_no', 'ASC')
            ->get();
        $callback = function () use ($candidates) {
            $file = fopen('php://output', 'w');
            foreach ($candidates as $candidate) {
                $newDate = explode("-", $candidate->date_of_birth);
                $dateofbirth = "";
                $dateofbirth .= $newDate[2];
                $dateofbirth .= $newDate[1];
                $dateofbirth .= $newDate[0];
                $candidate_array = [];
                $candidate_array[] = $candidate->center_no;
                $candidate_array[] = $candidate->candidate_no;
                $candidate_array[] = html_entity_decode($candidate->candidate_surname, ENT_QUOTES, "UTF-8");
                $candidate_array[] = html_entity_decode($candidate->candidate_other_name, ENT_QUOTES, "UTF-8");
                $candidate_array[] = $candidate->gender;
                $candidate_array[] =  $dateofbirth;
                fputcsv($file, $candidate_array);
            }
            fclose($file);
        };

        return  response()->stream($callback, 200, $headers);
    }

    public function exportCandidatesRegistration(Request $request)
    {
        $center = $request->has('center') ? $request->center : 'All Centers';
        $filename =  $center . ' - ' . time();
        $headers = array(
            "Content-type" => "text/plain",
            "Content-Disposition" => "attachment; filename=" . $filename . '.' . $request->file_format,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $candidates = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.candidate_no',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
           order by candidate_subject.subject_code separator ',') as subjects")
                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session');

        if ($request->has('date_from') && !is_null($request->date_from)) {
            $date = date("Y-m-d", strtotime($request->date_from));
            $candidates = $candidates->where('center_candidate.created_at', '>=', $date);
        }
        if ($request->has('center') && !is_null($request->center)) {
            $candidates = $candidates->where('center_candidate.center_no', '=',  $center);
        }

        if ($request->has('level') && !is_null($request->level)) {
            $candidates = $candidates->where('center_candidate.level', '=', $request->level);
        }
        if ($request->has('session') && !is_null($request->session)) {
            $candidates = $candidates->where('center_candidate.session', '=', $request->session);
        }

        if ($request->has('year') && !is_null($request->year)) {
            $candidates = $candidates->where('center_candidate.financial_year', '=', $request->year);
        }
        $candidates = $candidates->orderBy('center_candidate.center_no', 'ASC')
            ->orderBy('center_candidate.candidate_no', 'ASC')
            ->get();
        if ($candidates->isNotEmpty()) {
            $callback = function () use ($candidates) {
                $file = fopen('php://output', 'w');
                foreach ($candidates as $candidate) {
                    $newDate = explode("-", $candidate->date_of_birth);
                    $dateofbirth = "";
                    $dateofbirth .= $newDate[2];
                    $dateofbirth .= $newDate[1];
                    $dateofbirth .= $newDate[0];
                    $candidate_array = [];
                    $candidate_array[] = $candidate->center_no;
                    $candidate_array[] = $candidate->candidate_no;
                    $candidate_array[] = html_entity_decode($candidate->candidate_surname, ENT_QUOTES, "UTF-8");
                    $candidate_array[] = html_entity_decode($candidate->candidate_other_name, ENT_QUOTES, "UTF-8");
                    $candidate_array[] = " ";
                    $candidate_array[] = $candidate->type;
                    $candidate_array[] = $candidate->gender;
                    $candidate_array[] = " ";
                    $candidate_array[] =  $dateofbirth;
                    $candidate_array[] = " ";
                    $candidate_array[] =   $candidate->subject_number;
                    $subjects = explode(",", $candidate->subjects);
                    foreach ($subjects as $subject) {
                        $subjects_code = explode(" ", $subject);
                        $candidate_array[] = $subjects_code[0];
                        $candidate_array[] = $subjects_code[1];
                    }
                    $candidate_array[] =   $candidate->sponser == "P" ? "O" : $candidate->sponser;

                    fputcsv($file, $candidate_array);
                }
                fclose($file);
            };
            return  response()->stream($callback, 200, $headers);
        }
        return redirect(route('admin.candidate-registation.index'))->with('success', " No candidate registered at $center for $request->session and $request->level");
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


        $sponsors = DB::table('funders')
                    ->select(
                        'sponsor',
                        'name',
                        'description'
                    )->pluck('sponsor')->toArray();
        $validationRules = [
            1 => [
                'candidate_no' => ['required'],
                'level' => ['required'],
                'center_no' => ['required'],
                'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                'candidate_surname' => ['required'],
                'candidate_other_name' => ['required'],
                'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:-8 years'],
                'gender' => ['required', 'in:M,F'],
                'sponser' => ['required',  Rule::in($sponsors )],
                'type' => ['required', 'in:1,2,3'],
                'type' => ['required'],
                'session' => ['required'],
                'subject' => ['required'],
                'special_need' => ['required'],
            ],
            2 => [
                'email' => ['required', 'email'],
                'phone_number' => ['required', 'digits:8'],
                'postal_address' => ['required'],
                'physical_address' => ['required'],
                'village' => ['required'],
                'district' => ['required']
            ],
            3 => [
                "guardian_name" => ['required'],
                "guardian_surname" => ['required'],
                "guardian_email" => ['required', 'email'],
                "guardian_national_id" => ['required'],
                "guardian_phone_number" => ['required', 'digits:8'],
                "guardian_physical_address" => ['required'],
                "guardian_postal_address" => ['required'],
                "guardian_village" => ['required'],
                "guardian_district" => ['required'],
                "guardian_type" => ['required'],
            ],
            4 => []
        ];
        $validationMassages = [
            1 => [],
            2 => [],
            3 => [],
            4 => [],
        ];
        $tabs = $request->tabs;


        $errors = [];
        $formData = "";
        foreach ($tabs as $key => $tab) {
            $formData .= "$tab";
            parse_str(urldecode($tab), $data);
            $validator = Validator::make($data,  $validationRules[$key], $validationMassages[$key]);
            if ($validator->fails()) {
                $errors[$key] = ['errors' => $validator->errors()];
            }
            if (next($tabs)) {
                $formData .= "&";
            }
        }
        $requestData  = array();
        parse_str(urldecode($formData), $requestData);
        $request->merge($requestData);
        if (count($errors) > 0) {

            return response()->json(['errors' => $errors]);
        }

        $candidate_no = $request->candidate_no;
        $center = $request->center_no;
        $subjects = [];
        foreach ($request->subject as $subject) {
            $subjectoptioncode = explode(",", $subject);
            $subject_code = $subjectoptioncode[0];
            $subject_option = $subjectoptioncode[1];
            $subjects[] = array(
                'candidate_no' => $request->candidate_no,
                'subject_code' => (int)$subject_code,
                'subject_option' => $subject_option,
                'type' => $request->type
            );
        }


        $request->merge(["subjects" => $subjects]);
        $validation_rules['subjects'][] = new SubjectsGrouping($center);
        $validation_rules['subjects'][] = new CheckDupsSubject();
        $validation_messages = [];
        $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
        if ($validator->fails()) {
            $errors[1] = ['errors' => $validator->errors()];
            return response()->json(['errors' => $errors]);
        }

        $candidateUserArray = array();
        if ($request->candidate_no == "*") {
            $new_candidate = Candidate::whereDate('date_of_birth', date("Y-m-d", strtotime($request->date_of_birth)))
                ->where('candidate_other_name', '=', strtoupper($request->candidate_other_name))
                ->where('gender', '=', strtoupper($request->gender))
                ->where('candidate_surname', '=', strtoupper($request->candidate_surname))->first();

            $validation_rules['national_id'][] = Rule::unique('center_candidate')
                ->where(function ($query) use ($request) {
                    return $query->where('national_id', $request->national_id)
                        ->where('level', '=', $request->level)
                        ->where('session', '=', 'November')
                        ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                });
            $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            } else {
                $candidate_no = (!$new_candidate) ? getNextCandidateNumber() : $new_candidate->candidate_no;
                $request->merge(['candidate_no' => $candidate_no]);
                // Assign Candidate Number
                if (!$new_candidate) {
                    Candidate::create([
                        'candidate_no' => $request->candidate_no,
                        'national_id' => $request->national_id,
                        'candidate_surname' =>  strtoupper($request->candidate_surname),
                        'candidate_other_name' => strtoupper($request->candidate_other_name),
                        'date_of_birth' => date("Y-m-d", strtotime($request->date_of_birth)),
                        'gender' => $request->gender,
                    ]);
                } else {
                }
                $this->register($request);
            }
        } else {
            $validation_rules['candidate_no'][] = Rule::unique('center_candidate')
                ->where(function ($query) use ($request) {
                    return $query->where('candidate_no', $request->candidate_no)
                        ->where('level', '=', $request->level)
                        ->where('session', '=', 'November')
                        ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                });
            $validation_rules['candidate_surname'][] = Rule::exists('candidates', 'candidate_surname')
                ->where('candidate_no', $request->candidate_no);

            $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
            if ($validator->fails()) {
                $errors[1] = ['errors' => $validator->errors()];
                return response()->json(['errors' => $errors]);
            } else {
                $this->register($request);
            }
        }
        switch ($request->level) {
            case 'G7ELT':
                $candidate_arrangement = CandidateArrangement::where([
                    ["candidate_no", '=', $request->candidate_no]
                ])->first();
                if ($candidate_arrangement !== null) {
                    $candidate_arrangement->update(['arrangement_id' => $request->special_need]);
                } else {
                    $candidate_arrangement = CandidateArrangement::create([
                        'candidate_no' =>  $request->candidate_no,
                        'arrangement_id' => $request->special_need
                    ]);
                }


                $candidate_address = Address::where([
                    ['user_id', '=', $request->candidate_no],
                    ['user_type', '=', Candidate::class]
                ])->first();


                if ($candidate_address !== null) {
                    $candidate_address->update([
                        'user_id' => $request->national_id,
                        'user_type' => Candidate::class,
                        "postal_address" => $request->postal_address,
                        "physical_address" => $request->physical_address,
                        "village" => $request->village,
                        "district" => $request->district,

                    ]);
                } else {
                    $candidate_address = Address::create([
                        'user_id' => $request->national_id,
                        'user_type' => Candidate::class,
                        "postal_address" => $request->postal_address,
                        "physical_address" => $request->physical_address,
                        "village" => $request->village,
                        "district" => $request->district,
                    ]);
                }
                $guardian_detail = Guardian::where([
                    ['candidate', '=', $request->candidate_no],
                ])->first();
                if ($guardian_detail  !== null) {
                    $guardian_detail->update([
                        'national_id' => $request->guardian_national_id,
                        "guardian_type" => $request->guardian_type,
                        "name" => $request->guardian_name,
                        "surname" => $request->guardian_surname,
                        "email" => $request->guardian_email,
                        "phone_number" => $request->guardian_phone_number
                    ]);
                    $guardian_address = Address::where([
                        ['user_id', '=', $request->guardian_national_id],
                        ['user_type', '=', Guardian::class],
                    ])->first();
                    if ($guardian_address !== null) {
                        $guardian_address->update([
                            'user_id',
                            '=',
                            $request->guardian_national_id,
                            'user_type',
                            '=',
                            Guardian::class,
                            "postal_address" => $request->guardian_postal_address,
                            "physical_address" => $request->guardian_physical_address,
                            "village" => $request->guardian_village,
                            "user_id" => $request->guardian_national_id,
                            "district" => $request->guardian_district,
                        ]);
                    } else {
                        Address::create([
                            'user_id' => $request->guardian_national_id,
                            'user_type' => Guardian::class,
                            "postal_address" => $request->guardian_postal_address,
                            "physical_address" => $request->guardian_physical_address,
                            "village" => $request->guardian_village,
                            "user_id" => $request->guardian_national_id,
                            "district" => $request->guardian_district,
                        ]);
                    }
                } else {
                    Guardian::create([
                        "candidate" => $candidate_no,
                        'national_id' => $request->guardian_national_id,
                        "guardian_type" => $request->guardian_type,
                        "name" => $request->guardian_name,
                        "surname" => $request->guardian_surname,
                        "email" => $request->guardian_email,
                        "phone_number" => $request->guardian_phone_number
                    ]);
                    Address::create([
                        'user_id' => $request->guardian_national_id,
                        "postal_address" => $request->guardian_postal_address,
                        "physical_address" => $request->guardian_physical_address,
                        "village" => $request->guardian_village,
                        "user_id" => $request->guardian_national_id,
                        "user_type" => Guardian::class,
                        "district" => $request->guardian_district,
                    ]);
                }
                break;
            case 'LGCSE':
                // Create User Profile
                $candidateUserArray[] = [
                    'national_id' => $request->national_id,
                    'candidate_no' =>  $request->candidate_no,
                    'center_no' =>  $center,
                    'username' => $request->candidate_surname . " " . $request->candidate_other_name,
                    'password' =>  Hash::make(str_replace('-', '', date("Y-m-d", strtotime($request->date_of_birth)))),
                    'candidate_password' => str_replace('-', '', date("Y-m-d", strtotime($request->date_of_birth))),
                    'session' => 'November',
                    'financial_year' => date('Y') . '-' . (date('Y') + 1),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
                insertOrUpdate('candidate_users', $candidateUserArray);
                $candidate_arrangement = CandidateArrangement::where([
                    ["candidate_no", '=', $request->candidate_no]
                ])->first();
                if ($candidate_arrangement !== null) {
                    $candidate_arrangement->update(['arrangement_id' => $request->special_need]);
                } else {
                    $candidate_arrangement = CandidateArrangement::create([
                        'candidate_no' =>  $request->candidate_no,
                        'arrangement_id' => $request->special_need
                    ]);
                }


                $candidate_address = Address::where([
                    ['user_id', '=',   $request->national_id],
                    ['user_type', '=', Candidate::class]
                ])->first();


                if ($candidate_address !== null) {
                    $candidate_address->update([
                        'user_id' => $request->national_id,
                        'user_type' => Candidate::class,
                        "postal_address" => $request->postal_address,
                        "physical_address" => $request->physical_address,
                        "village" => $request->village,
                        "district" => $request->district,

                    ]);
                } else {
                    $candidate_address = Address::create([
                        'user_id' => $request->national_id,
                        'user_type' => Candidate::class,
                        "postal_address" => $request->postal_address,
                        "physical_address" => $request->physical_address,
                        "village" => $request->village,
                        "district" => $request->district,
                    ]);
                }

                $guardian_detail = Guardian::where([
                    ['candidate', '=', $request->candidate_no],
                ])->first();



                if ($guardian_detail  !== null) {
                    $guardian_detail->update([
                        'national_id' => $request->guardian_national_id,
                        "guardian_type" => $request->guardian_type,
                        "name" => $request->guardian_name,
                        "surname" => $request->guardian_surname,
                        "email" => $request->guardian_email,
                        "phone_number" => $request->guardian_phone_number
                    ]);

                    $guardian_address = Address::where([
                        ['user_id', '=', $request->guardian_national_id],
                        ['user_type', '=', Guardian::class],
                    ])->first();




                    if ($guardian_address !== null) {
                        $guardian_address->update([
                            'user_id',
                            '=',
                            $request->guardian_national_id,
                            'user_type',
                            '=',
                            Guardian::class,
                            "postal_address" => $request->guardian_postal_address,
                            "physical_address" => $request->guardian_physical_address,
                            "village" => $request->guardian_village,
                            "user_id" => $request->guardian_national_id,
                            "district" => $request->guardian_district,
                        ]);
                    } else {
                        Address::create([
                            'user_id' => $request->guardian_national_id,
                            'user_type' => Guardian::class,
                            "postal_address" => $request->guardian_postal_address,
                            "physical_address" => $request->guardian_physical_address,
                            "village" => $request->guardian_village,
                            "user_id" => $request->guardian_national_id,
                            "district" => $request->guardian_district,
                        ]);
                    }
                } else {
                    Guardian::create([
                        "candidate" => $candidate_no,
                        'national_id' => $request->guardian_national_id,
                        "guardian_type" => $request->guardian_type,
                        "name" => $request->guardian_name,
                        "surname" => $request->guardian_surname,
                        "email" => $request->guardian_email,
                        "phone_number" => $request->guardian_phone_number
                    ]);
                    Address::create([
                        'user_id' => $request->guardian_national_id,
                        "postal_address" => $request->guardian_postal_address,
                        "physical_address" => $request->guardian_physical_address,
                        "village" => $request->guardian_village,
                        "user_id" => $request->guardian_national_id,
                        "user_type" => Guardian::class,
                        "district" => $request->guardian_district,
                    ]);
                }

                break;
            default:
                break;
        }
        return response()->json(['success' => "You have successfully added candidate"]);
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
        $candidate = $this->is_registered($id);
        $center_no =  $candidate->center_no;
        $prifix = substr(date('Y'), 2, 4);
        $editable = substr($candidate->candidate_no, 0, 2) == $prifix ? true : false;
        // $url = route('center.candidates.update', $id);
        $url = route('admin.candidate-registation.update', $id);
        $level = Level::where('level', '=', $candidate->level)
            ->first()->id;
        $session = Session::where('session', '=', $candidate->session)
            ->where('financial_year', '=', $candidate->financial_year)->first()->id;
        $candidateArray = array();
        $request = new Request($candidateArray);
        $request->merge(["session" => $session]);
        $request->merge(["level" => $level]);
        $request->merge(["appending_subjects" => $id]);
        $request->merge(["candidate_id" => $id]);
        $request->merge(["centre_no" =>   $center_no]);
        $editable_fields = ['national_id', 'candidate_surname', 'candidate_other_name', 'date_of_birth', 'gender'];
        $subjectsHTML = $this->centersubjects($request)?->original;
        $guardian = DB::table('guardians')
            ->select(
                [
                    'guardians.national_id',
                    'guardians.guardian_type',
                    'guardians.name',
                    'guardians.surname',
                    DB::raw('guardians.guardian_type as type'),
                    'guardians.phone_number',
                    'guardians.email',
                    'addresses.postal_address',
                    'addresses.physical_address',
                    'addresses.district',
                    'addresses.village'
                ],
            )
            ->join('center_candidate', 'center_candidate.candidate_no', '=', 'guardians.candidate')
            ->join('guardian_type', 'guardian_type.id', '=', 'guardians.guardian_type')
            ->join('addresses', function ($join) {
                $join->on('guardians.national_id', '=', 'addresses.user_id');
                $join->where('addresses.user_type', '=', Guardian::class);
            })
            ->where("guardians.candidate", '=',  $candidate->candidate_no)
            ->first();

        return response()->json([
            'candidate' => $candidate,
            'action' => $url,
            $subjectsHTML,
            'editable' => $editable,
            'editable_fields' => $editable_fields,
            'guardian' => $guardian,
            'paid_fee' =>  $candidate->amount
        ]);
    }


    public function searchCandidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_no' => 'required|numeric|exists:candidates,candidate_no',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $candidate_registration = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.candidate_no',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
       order by candidate_subject.subject_code separator ',') as subjects")
                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->where("center_candidate.candidate_no", '=', $request->candidate_no)
            ->where("center_candidate.financial_year", '=', $financial_year)
            ->get();
        $candidate =  Candidate::findOrFail($request->candidate_no);


        if (count($candidate_registration) == 1) {
            $candidate_registration = $candidate_registration->first();
            $session_html = $candidate_registration->session == "November"  ? "<option value='June'>June</option>" : "<option value='November'>November</option>";
            $output =  "<div class='row'>

                                <div class='form-group col-md-4'>
                                    <label for=' '>Candidate Number  </label>
                                    <input type='text' class='form-control' name='candidate_number' id='inputEmail4' disabled value='" . str_pad($candidate->candidate_no, 9, '0', STR_PAD_LEFT) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-4'>
                                    <label for='surname'>Surname</label>
                                    <input type='text' class='form-control' name='surname' id='surname' disabled value='" . htmlspecialchars($candidate->candidate_surname, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-4'>
                                    <label for='other_name'>Other name</label>
                                    <input type='text' class='form-control' name='other_name' id='other_name' disabled value='" . htmlspecialchars($candidate->candidate_other_name, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                        </div>
                        <div class='row'>
                                <div class='form-group col-md-6'>
                                    <label for=' '>Date of birth</label>
                                    <input type='text' class='form-control' id='inputEmail4' disabled value='$candidate->date_of_birth'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-6'>
                                    <label for='gender'>Gender</label>
                                    <input type='text' class='form-control' name='gender' id='gender' disabled value='  $candidate->gender'>
                                </div>
                        </div>
                        <div class='row'>
                        <div class='form-group col-md-4'>
                                <label for='type'>Type</label>
                                <select name='type' class='form-control' id='type'>
                                <option value=''>Select Type</option>
                                <option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                </select>
                            <span class='help-block'></span>
                        </div>
                        <div class='form-group col-md-4'>
                            <label for='sponsor'>Sponsor</label>
                            <select name='sponsor' class='form-control' id='sponsor'>
                            <option value=''>Select Sponsor</option>
                            <option value='O'>O</option>
                            <option value='M'>M</option>
                            <option value='N'>N</option>
                            </select>
                            <span class='help-block'></span>
                        </div>
                        <div class='form-group col-md-4'>
                                <label for='session'>Session</label>
                                <select name='session' class='form-control' id='session'>
                                    <option value=''>Select session</option>
                                    $session_html
                                </select>
                            <span class='help-block'></span>
                        </div>
                    </div>
                    <div class='row subjects-errors'>
                        <div class='form-group  col-md-4'>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0175,ENGLISH LANGUAGE' id='english_lang'>
                                <label  for='english_lang'>ENGLISH LANGUAGE (0175)</label>
                            </div>
                            <div class='checkbox''>
                                <input type='checkbox' name='subject[]' value='0185,LITERATURE IN ENGLISH'  id='english_lit'>
                                <label  for='english_lit'>LITERATURE IN ENGLISH (0185)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='physcial_science[]' value='0181,PHYSCICAL-SCIENCE (Core)'  id='physical_science_core'>
                                <label  for='physical_science_core'>PHYSICAL SCIENCE (Core, 0181)</label>
                            </div>
                            <div class='checkbox''>
                                <input type='checkbox' name='physcial_science[]' value='0181,PHYSCICAL-SCIENCE (Extendend)'  id='physical_science_extended'>
                                <label  for='physical_science_extended'>PHYSICAL SCIENCE (Extendend, 0181)</label>
                            </div>

                            <div class='checkbox''>
                                <input type='checkbox' name='mathematics[]' value='0178,MATHEMATICS (Core)'  id='maths_core'>
                                <label  for='maths_core'>MATHEMATICS (Core, 0178)</label>
                            </div>
                            <div class='checkbox''>
                                <input type='checkbox' name='mathematics[]' value='0178,MATHEMATICS (Extendend)'  id='maths_extended'>
                                <label  for='maths_extended'>MATHEMATICS (Extendend, 0178)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0176,SESOTHO' id='sesotho'>
                                <label  for='sesotho'>SESOTHO (0176)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0198,PHYSICS' id='physics'>
                                <label  for='physics'>PHYSICS (0198)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0197,CHEMISTRY' id='chemistr'>
                                <label  for='chemistry'>CHEMISTRY (0197)</label>
                            </div>
                        </div>
                        <div class='form-group  col-md-4'>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0180,BIOLOGY'  id='biology'>
                                <label  for='biology'>BIOLOGY (0180)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0184,HISTORY'  id='history'>
                                <label  for='history'>HISTORY (0184)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0183,GEOGRAPHY'  id='geography'>
                                <label  for='geography'>GEOGRAPHY (0183)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0455,ECONOMICS ' id='economics'>
                                <label  subject_label' for='economics'>ECONOMICS (0455)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0179,AGRICULTURE'  id='agric'>
                                <label  for='agric'>AGRICULTURE (0179)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0182,HISTORY'  id='development_studies'>
                                <label  for='development_studies'>DEVELOPMENT STUDIES (0182)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0194,TRAVEL AND TOURISM'  id='travelAndTourism'>
                                <label  for='travelAndTourism''>TRAVEL AND TOURISM (0194)</label>
                            </div>

                        </div>
                        <div class='form-group  col-md-4'>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0417,ICT' id='ICT'>
                                <label  ' for='ICT'>ICT (0417)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0450,BUSINESS STUDIES' id='bs'>
                                <label  for='bs'>BUSINESS STUDIES (0450)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0186,RELIGIOUS STUDIES' id='religious'>
                                <label  for='religious'>RELIGIOUS STUDIES (0186)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0187,ACCOUNTING' id='acc'>
                                <label  for='acc'>ACCOUNTING (0187)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0189,DESIGN AND TECHNOLOGY' id='DT'>
                                <label  for='DT'>DESIGN AND TECHNOLOGY (0189)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0191,FASHION AND TEXTILES' id='ft'>
                                <label  for='ft'>FASHION AND TEXTILES (0191)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0192,FOOD AND NUTRITION' id='fn'>
                                <label  for='fn'>FOOD AND NUTRITION (0192)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='2030,LIFE SKILLS BASED SEXUALITY EDUCATION' id='lbse'>
                                <label  for='lbse'>LIFE SKILLS BASED SEXUALITY EDUCATION (2030)</label>
                            </div>

                        </div>
                        <span class='help-block'></span>
                    </div>";
            $status = 1;
        } elseif (count($candidate_registration) == 2) {
            return response()->json(['errors' => ['candidate' => ['candidate already registerd']]]);
        } else {
            $output =  "<div class='row'>
                                <div class='form-group col-md-4'>
                                    <label for=' '>Candidate Number</label>
                                    <input type='text' class='form-control' name='candidate_number' id='inputEmail4' disabled value='" . str_pad($candidate->candidate_no, 9, '0', STR_PAD_LEFT) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-4'>
                                    <label for='surname'>Surname</label>
                                    <input type='text' class='form-control' name='surname' id='surname' disabled value='" . htmlspecialchars($candidate->candidate_surname, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-4'>
                                    <label for='other_name'>Other name</label>
                                    <input type='text' class='form-control' name='other_name' id='other_name' disabled value='" . htmlspecialchars($candidate->candidate_other_name, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                        </div>
                        <div class='row'>
                                <div class='form-group col-md-6'>
                                    <label for=' '>Date of birth</label>
                                    <input type='text' class='form-control' id='inputEmail4' disabled value='$candidate->date_of_birth'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-6'>
                                    <label for='gender'>Gender</label>
                                    <input type='text' class='form-control' name='gender' id='gender' disabled value='  $candidate->gender'>
                                </div>
                        </div>
                        <div class='row'>
                        <div class='form-group col-md-4'>
                                <label for='type'>Type</label>
                                <select name='type' class='form-control' id='type'>
                                <option value=''>Select Type</option>
                                <option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                </select>
                            <span class='help-block'></span>
                        </div>
                        <div class='form-group col-md-4'>
                            <label for='sponsor'>Sponsor</label>
                            <select name='sponsor' class='form-control' id='sponsor'>
                            <option value=''>Select Sponsor</option>
                            <option value='O'>O</option>
                            <option value='M'>M</option>
                            <option value='N'>N</option>
                            </select>
                            <span class='help-block'></span>
                        </div>
                        <div class='form-group col-md-4'>
                                <label for='session'>Session</label>
                                <select name='session' class='form-control' id='session'>
                                    <option value=''>Select session</option>
                                    <option value='June'>June</option>
                                    <option value='November'>November</option>
                                </select>
                            <span class='help-block'></span>
                        </div>
                    </div>
                    <div class='row subjects-errors'>
                        <div class='form-group  col-md-4'>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0175,ENGLISH LANGUAGE' id='english_lang'>
                                <label  for='english_lang'>ENGLISH LANGUAGE (0175)</label>
                            </div>
                            <div class='checkbox''>
                                <input type='checkbox' name='subject[]' value='0185,LITERATURE IN ENGLISH'  id='english_lit'>
                                <label  for='english_lit'>LITERATURE IN ENGLISH (0185)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='physcial_science[]' value='0181,PHYSCICAL-SCIENCE (Core)'  id='physical_science_core'>
                                <label  for='physical_science_core'>PHYSICAL SCIENCE (Core, 0181)</label>
                            </div>
                            <div class='checkbox''>
                                <input type='checkbox' name='physcial_science[]' value='0181,PHYSCICAL-SCIENCE (Extendend)'  id='physical_science_extended'>
                                <label  for='physical_science_extended'>PHYSICAL SCIENCE (Extendend, 0181)</label>
                            </div>

                            <div class='checkbox''>
                                <input type='checkbox' name='mathematics[]' value='0178,MATHEMATICS (Core)'  id='maths_core'>
                                <label  for='maths_core'>MATHEMATICS (Core, 0178)</label>
                            </div>
                            <div class='checkbox''>
                                <input type='checkbox' name='mathematics[]' value='0178,MATHEMATICS (Extendend)'  id='maths_extended'>
                                <label  for='maths_extended'>MATHEMATICS (Extendend, 0178)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0176,SESOTHO' id='sesotho'>
                                <label  for='sesotho'>SESOTHO (0176)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0198,PHYSICS' id='physics'>
                                <label  for='physics'>PHYSICS (0198)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0197,CHEMISTRY' id='chemistr'>
                                <label  for='chemistry'>CHEMISTRY (0197)</label>
                            </div>
                        </div>
                        <div class='form-group  col-md-4'>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0180,BIOLOGY'  id='biology'>
                                <label  for='biology'>BIOLOGY (0180)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0184,HISTORY'  id='history'>
                                <label  for='history'>HISTORY (0184)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0183,GEOGRAPHY'  id='geography'>
                                <label  for='geography'>GEOGRAPHY (0183)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0455,ECONOMICS ' id='economics'>
                                <label  subject_label' for='economics'>ECONOMICS (0455)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0179,AGRICULTURE'  id='agric'>
                                <label  for='agric'>AGRICULTURE (0179)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0182,HISTORY'  id='development_studies'>
                                <label  for='development_studies'>DEVELOPMENT STUDIES (0182)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0194,TRAVEL AND TOURISM'  id='travelAndTourism'>
                                <label  for='travelAndTourism''>TRAVEL AND TOURISM (0194)</label>
                            </div>

                        </div>
                        <div class='form-group  col-md-4'>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0417,ICT' id='ICT'>
                                <label  ' for='ICT'>ICT (0417)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0450,BUSINESS STUDIES' id='bs'>
                                <label  for='bs'>BUSINESS STUDIES (0450)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0186,RELIGIOUS STUDIES' id='religious'>
                                <label  for='religious'>RELIGIOUS STUDIES (0186)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0187,ACCOUNTING' id='acc'>
                                <label  for='acc'>ACCOUNTING (0187)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0189,DESIGN AND TECHNOLOGY' id='DT'>
                                <label  for='DT'>DESIGN AND TECHNOLOGY (0189)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0191,FASHION AND TEXTILES' id='ft'>
                                <label  for='ft'>FASHION AND TEXTILES (0191)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0192,FOOD AND NUTRITION' id='fn'>
                                <label  for='fn'>FOOD AND NUTRITION (0192)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='2030,LIFE SKILLS BASED SEXUALITY EDUCATION' id='lbse'>
                                <label  for='lbse'>LIFE SKILLS BASED SEXUALITY EDUCATION (2030)</label>
                            </div>

                        </div>
                        <span class='help-block'></span>
                    </div>";
            $status = 1;
        }


        return response()->json(['status' =>  $status, 'html' =>  $output]);
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

        $sponsors = DB::table('funders')
                    ->select(
                        'sponsor',
                        'name',
                        'description'
                    )->pluck('sponsor')->toArray();
        $validationRules = [
            1 => [
                'candidate_no' => ['required'],
                'level' => ['required'],
                'center_no' => ['required'],
                'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                'candidate_surname' => ['required'],
                'candidate_other_name' => ['required'],
                'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:-8 years'],
                'gender' => ['required', 'in:M,F'],
                 'sponser' => ['required',  Rule::in($sponsors )],
                'type' => ['required', 'in:1,2,3'],
                'type' => ['required'],
                'session' => ['required'],
                'subject' => ['required'],
                'special_need' => ['required'],
            ],
            2 => [
                'email' => ['required', 'email'],
                'phone_number' => ['required', 'digits:8'],
                'postal_address' => ['required'],
                'physical_address' => ['required'],
                'village' => ['required'],
                'district' => ['required']
            ],
            3 => [
                "guardian_name" => ['required'],
                "guardian_surname" => ['required'],
                "guardian_email" => ['required', 'email'],
                "guardian_national_id" => ['required'],
                "guardian_phone_number" => ['required', 'digits:8'],
                "guardian_physical_address" => ['required'],
                "guardian_postal_address" => ['required'],
                "guardian_village" => ['required'],
                "guardian_district" => ['required'],
                "guardian_type" => ['required'],
            ],
            4 => []
        ];
        $validationMassages = [
            1 => [],
            2 => [],
            3 => [],
            4 => [],
        ];
        $tabs = $request->tabs;
        $errors = [];
        $formData = "";
        foreach ($tabs as $key => $tab) {
            $formData .= "$tab";
            parse_str(urldecode($tab), $data);
            $validator = Validator::make($data,  $validationRules[$key], $validationMassages[$key]);
            if ($validator->fails()) {
                $errors[$key] = ['errors' => $validator->errors()];
            }
            if (next($tabs)) {
                $formData .= "&";
            }
        }
        $requestData  = array();
        parse_str(urldecode($formData), $requestData);
        $request->merge($requestData);
        if (count($errors) > 0) {
            return response()->json(['errors' => $errors]);
        }

        $candidate_no = $request->candidate_no;
        $center = $request->center_no;
        $subjects = [];
        $validation_messages = [];
        $validation_rules = [];
        foreach ($request->subject as $subject) {
            $subjectoptioncode = explode(",", $subject);
            $subject_code = $subjectoptioncode[0];
            $subject_option = $subjectoptioncode[1];
            $subjects[] = array(
                'candidate_no' => $request->candidate_no,
                'subject_code' => (int)$subject_code,
                'subject_option' => $subject_option,
                'type' => $request->type
            );
        }
        $request->merge(["subjects" => $subjects]);
        $validation_rules['subjects'][] = new SubjectsGrouping($center);
        $validation_rules['subjects'][] = new CheckDupsSubject();
        $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
        if ($validator->fails()) {
            return response()->json(['errors' => [1 => $validator->errors()]]);
        }
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.national_id',
                    'center_candidate.candidate_no',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.financial_year',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
            order by candidate_subject.subject_code separator ',') as subjects")
                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->where("center_candidate.id", '=', $id)
            ->first();
        $registered_subjects = $candidate->subjects;
        $subject_number = count($request->subjects);
        //Candidates
        Candidate::where('candidate_no', '=', $candidate_no)
            ->update([
                'national_id' => $request->national_id,
                'candidate_surname' => strtoupper($request->candidate_surname),
                'candidate_other_name' => strtoupper($request->candidate_other_name),
                'date_of_birth' => date("Y-m-d", strtotime($request->date_of_birth)),
                'gender' => $request->gender,
            ]);

        //Candidate User
        CandidateUser::updateOrCreate(
            [
                'candidate_no' =>  $request->candidate_no,
                'center_no' => $candidate->center_no,
                'session' => $candidate->session,
                'financial_year' => $candidate->financial_year,
            ],
            [
                'national_id' => $request->national_id,
                'username' => $request->candidate_surname . " " . $request->candidate_other_name,
                'password' =>  Hash::make(str_replace('-', '', date("Y-m-d", strtotime($request->date_of_birth)))),
                'candidate_password' => str_replace('-', '', date("Y-m-d", strtotime($request->date_of_birth))),
            ]
        );
        //Special Needs
        CandidateArrangement::updateOrCreate(
            ['candidate_no' =>  $request->candidate_no],
            ['arrangement_id' => $request->special_need]
        );
        //Candidate Address
        Address::updateOrCreate(
            [
                'user_id' => $candidate->national_id,
                'user_type' => Candidate::class
            ],
            [
                "postal_address" => $request->postal_address,
                "physical_address" => $request->physical_address,
                "village" => $request->village,
                "district" => $request->district,
            ]
        );

        //Guardian
        Guardian::updateOrCreate(
            [
                'candidate'=>$request->candidate_no,
                'national_id' => $request->guardian_national_id,
            ],
            [
                "guardian_type" => $request->guardian_type,
                "name" => $request->guardian_name,
                "surname" => $request->guardian_surname,
                "email" => $request->guardian_email,
                "phone_number" => $request->guardian_phone_number
            ]
        );
        Address::updateOrCreate(
            [
                'user_id'=>$request->guardian_national_id,
                'user_type'=> Guardian::class
            ],
            [
                "postal_address" => $request->guardian_postal_address,
                "physical_address" => $request->guardian_physical_address,
                "village" => $request->guardian_village,
                "user_id" => $request->guardian_national_id,
                "district" => $request->guardian_district,
            ]
        );

        //Center Candidate
        $candidate = CenterCandidate::find($id);
        $candidate->center_no =  $center;
        $candidate->subject_number = $subject_number;
        $candidate->type = $request->type;
        $candidate->sponser = $request->sponser;
        $candidate->national_id = $request->national_id;
        $candidate->phone_number = $request->phone_number;
        $candidate->email = $request->email;
        $candidate->level = $request->level;
        $candidate->session = $request->session;
        $candidate->save();
        //  Explode
        $subject = array_map(function ($input) {
            return explode(' ', $input);
        }, explode(',', $registered_subjects));

        $subject_registered_codes = array_reduce($subject, function ($array, $element) {
            $array[] = (int)$element[0];
            return $array;
        });
        $subject_codes = array_reduce($request->subjects, function ($array, $element) {
            $array[] = $element['subject_code'];
            return $array;
        });
        $not_registered_subjects = array_values(array_diff($subject_registered_codes, $subject_codes));
        SubjectCandidate::where('candidate_no', '=', $candidate->candidate_no)
            ->whereIn('subject_code', $not_registered_subjects)
            ->where('session', '=', $candidate->session)
            ->where('level', '=', $candidate->level)
            ->where('financial_year', '=', $candidate->financial_year)
            ->delete();
        foreach ($request->subjects as  $value) {
            SubjectCandidate::updateOrCreate(
                [
                    'candidate_no' =>  $request->candidate_no,
                    'subject_code' => $value['subject_code'],
                    'level' => $request->level,
                    'session' => $request->session,
                    'financial_year' => $candidate->financial_year,
                ],
                [
                    'candidate_no' => $request->candidate_no,
                    'national_id' => $request->national_id,
                    'subject_code' => $value['subject_code'],
                    'subject_option' => $value['subject_option'],
                    'level' => $request->level,
                    'session' => $request->session,
                    'financial_year' =>  $candidate->financial_year,
                ]
            );
        }
        return response()->json(['success' => 'Successfully updated the records']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.candidate_no',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
                    order by candidate_subject.subject_code separator ',') as subjects")
                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->where("center_candidate.id", '=', $id)
            ->first();
        SubjectCandidate::where('candidate_no', '=', $candidate->candidate_no)
            ->where('session', '=', $candidate->session)
            ->where('level', '=', $candidate->level)
            ->where('financial_year', '=', $financial_year)
            ->delete();
        CenterCandidate::where('center_candidate.id', '=', $id)->delete();
        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);
    }

    public function centersubjects(Request $request)
    {
        $session_id = $request->session;
        $level_id = $request->level;
        $center_no = $request->centre_no;
        if (empty($center_no)) {
            return response()->json(['subjectsHTML' => ""]);
        } else {
            $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
            $arraySessions = json_decode($center->sessions, TRUE);
            $optionHeaders = OptionHeader::get();
            $allSessionSubjects = DB::table('sessions')
                ->select(
                    'subjects.subject_code',
                    'subject_option.option_code',
                    'subjects.subject_name',
                    'subjects.short_name',
                    'subjects.level',
                    'sessions.id',
                    'sessions.session'
                )
                ->join('session_subject', 'sessions.id', '=', 'session_subject.session_id')
                ->join('subjects', 'subjects.subject_code', '=', 'session_subject.subject_code')
                ->join('subject_option', 'subject_option.subject_code', '=', 'subjects.subject_code')
                ->join('option_heads', 'option_heads.option_code', '=', 'subject_option.option_code');
            $optionsSubjects = $allSessionSubjects
                ->where('sessions.id', '=',  $session_id)
                ->where('subjects.level', '=', $level_id)
                ->whereIn('sessions.session', $arraySessions)
                ->get();

            $allSubjectArray = $optionsSubjects->pluck('subject_code')->toArray();
            $doubleOptionsSubjects = array_values(array_unique(array_diff_assoc($allSubjectArray, array_unique($allSubjectArray))));
            $allSessionSubjects = $allSessionSubjects
                ->where('sessions.id', '=',  $session_id)
                ->groupBy('subjects.subject_code')
                ->where('subjects.level', '=', $level_id)
                ->whereIn('sessions.session', $arraySessions)
                ->get();

            $candidateRegistedSubjects = array();
            if (!empty($request->appending_subjects)) {
                $candidate = $this->is_registered($request->candidate_id);
                $candidateSubjects = explode(',', $candidate->subjects);
                foreach ($candidateSubjects as $subject) {
                    $subject = explode('-', $subject);
                    $candidateRegistedSubjects[$subject[1]] = $subject[3];
                }
            }
            $centerSubjects = $center->subjects->pluck('subject_code')->toArray();
            $subjectsHTML = "";
            $is_subjects = false;
            $leftSubject = "<div class='col-md-6'>
                         <ul class='list-group'>";
            $closingTag = "</ul>
                </div>";
            $rightSubject = "<div class='col-md-6'>
                        <ul class='list-group'>";
            foreach ($allSessionSubjects as $key => $subject) {

                if (in_array($subject->subject_code, $centerSubjects)) {
                    $checkedSubject = isset($candidateRegistedSubjects[$subject->subject_code]) ? "checked" : "";
                    $is_subjects = true;
                    $subjectsHTML = "";
                    if ($key % 2 == 0) {
                        if (in_array($subject->subject_code, $doubleOptionsSubjects)) {
                            foreach ($optionHeaders as $optionHeader) {
                                $checkedSubject = isset($candidateRegistedSubjects[$subject->subject_code]) && $candidateRegistedSubjects[$subject->subject_code] == $optionHeader->option_code  ? "checked" : "";
                                if ($checkedSubject) {
                                    $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                                <input type='checkbox' name='subject[]' $checkedSubject  class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$optionHeader->option_code,$subject->subject_name' id='$optionHeader->option_code-$subject->subject_code'>
                                                <label class='custom-control-label' for='$optionHeader->option_code-$subject->subject_code'>$subject->subject_name- $optionHeader->description</label>
                                            </li>";
                                } else {
                                    $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                            <input type='checkbox' name='subject[]' $checkedSubject  class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$optionHeader->option_code,$subject->subject_name' id='$optionHeader->option_code-$subject->subject_code'>
                                            <label class='custom-control-label' for='$optionHeader->option_code-$subject->subject_code'>$subject->subject_name- $optionHeader->description</label>
                                        </li>";
                                }
                            }
                        } else {
                            $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                            <input type='checkbox' name='subject[]' $checkedSubject  class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$subject->option_code,$subject->subject_name' id='$subject->option_code-$subject->subject_code'>
                                            <label class='custom-control-label' for='$subject->option_code-$subject->subject_code'>$subject->subject_name</label>
                                        </li>";
                        }
                        $leftSubject .= $subjectsHTML;
                    } else {
                        if (in_array($subject->subject_code, $doubleOptionsSubjects)) {
                            foreach ($optionHeaders as $optionHeader) {
                                $checkedSubject = isset($candidateRegistedSubjects[$subject->subject_code]) && $candidateRegistedSubjects[$subject->subject_code] == $optionHeader->option_code  ? "checked" : "";
                                if ($checkedSubject) {
                                    $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                    <input type='checkbox' name='subject[]'  $checkedSubject  class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$optionHeader->option_code,$subject->subject_name' id='$optionHeader->option_code-$subject->subject_code'>
                                    <label class='custom-control-label' for='$optionHeader->option_code-$subject->subject_code'>$subject->subject_name- $optionHeader->description</label>
                                  </li>";
                                } else {
                                    $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                    <input type='checkbox' name='subject[]'  $checkedSubject  class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$optionHeader->option_code,$subject->subject_name' id='$optionHeader->option_code-$subject->subject_code'>
                                    <label class='custom-control-label' for='$optionHeader->option_code-$subject->subject_code'>$subject->subject_name- $optionHeader->description</label>
                                  </li>";
                                }
                            }
                        } else {


                            $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                            <input type='checkbox' name='subject[]' $checkedSubject  class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$subject->option_code,$subject->subject_name' id='$subject->option_code-$subject->subject_code'>
                                            <label class='custom-control-label' for='$subject->option_code-$subject->subject_code'>$subject->subject_name</label>
                                        </li>";
                        }
                        $rightSubject .= $subjectsHTML;
                    }
                }
            }
            if ($is_subjects) {
                $html = "";
                $leftSubject .=   $closingTag;
                $rightSubject .=   $closingTag;

                $html .= $leftSubject . $rightSubject;

                return response()->json(['subjectsHTML' =>  $html, 'doubleOptionsSubjects' => $candidateRegistedSubjects, 'disableSubject' => $request->all()]);
            } else {
                $session = $center->session;
                $subjectsHTML = "<div class='alert alert-danger' role='alert'>
                                      No subject for select session   $session
                            </div>
                            ";
                return response()->json(['subjectsHTML' => $subjectsHTML]);
            }
        }
    }

    private function register($request)
    {


        $subject_number = count($request->subjects);
        $center = $request->center_no;
        $candidate = new CenterCandidate();
        $candidate->candidate_no = $request->candidate_no;
        $candidate->national_id = $request->national_id;
        $candidate->center_no =  $center;
        $candidate->subject_number = $subject_number;
        $candidate->type = $request->type;
        $candidate->session = $request->session;
        $candidate->email = $request->email;
        $candidate->phone_number = $request->phone_number;
        $candidate->sponser = $request->sponser;
        $candidate->financial_year  = date('Y') . '-' . (date('Y') + 1);
        $candidate->level = $request->level;
        $candidate->save();
        foreach ($request->subjects as  $value) {
            SubjectCandidate::updateOrCreate(
                [
                    'candidate_no' =>  $request->candidate_no,
                    'national_id' =>   $request->national_id,
                    'subject_code' => $value['subject_code'],
                    'level' => $request->level,
                    'session' => $request->session,
                    'financial_year' =>  date('Y') . '-' . (date('Y') + 1),
                ],
                [
                    'candidate_no' => $request->candidate_no,
                    'national_id' => $request->national_id,
                    'subject_code' => $value['subject_code'],
                    'subject_option' => $value['subject_option'],
                    'level' => $request->level,
                    'session' => $request->session,
                    'financial_year' =>  date('Y') . '-' . (date('Y') + 1),
                ]
            );
        }
        return true;
    }

    public function is_registered($id)
    {
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.center_no',
                    'center_candidate.center_no',
                    'centers.center_name',
                    'center_candidate.id',
                    'subjects.subject_name',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.financial_year',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
                    fee_candidate_histories.candidate_id = center_candidate.id
                    and fee_candidate_histories.status='1'
                    ),0) AS  amount"),
                    DB::raw('candidate_arrangement.arrangement_id as special_need'),
                    'center_candidate.email',
                    'center_candidate.phone_number',
                    'addresses.postal_address',
                    'addresses.physical_address',
                    'addresses.district',
                    'addresses.village',
                    DB::raw("group_concat(DISTINCT concat(subjects.subject_name,'-',candidate_subject.subject_code,'-',option_heads.description,'-',candidate_subject.subject_option)
                  order by candidate_subject.subject_code separator ',') as subjects")
                ],

            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('subjects', 'subjects.subject_code', '=', 'candidate_subject.subject_code')
            ->join('option_heads', 'option_heads.option_code', '=', 'candidate_subject.subject_option')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.national_id', '=', 'center_candidate.national_id');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->leftJoin('addresses', function ($join) {
                $join->on('candidate_subject.national_id', '=', 'addresses.user_id');
                $join->where('addresses.user_type', '=', Candidate::class);
            })
            ->leftJoin('candidate_arrangement', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'candidate_arrangement.candidate_no');
            })


            ->leftJoin('fee_candidate_histories', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'fee_candidate_histories.candidate_id')
                    ->where('fee_candidate_histories.status', '=', 1);
            })
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->where("center_candidate.id", '=', $id)
            ->first();

        if ($candidate) {
            return  $candidate;
        } else {
            return false;
        }
    }
}
