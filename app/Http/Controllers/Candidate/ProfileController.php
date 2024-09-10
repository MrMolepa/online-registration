<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Candidate;
use App\Models\CandidateArrangement;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Guardian;
use App\Models\GuardianType;
use App\Models\SpecialNeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //
    public function index()
    {
        $national_id = auth()->user()->national_id;


        $candidate_no = auth()->user()->candidate_no;
        $specialNeeds = SpecialNeed::get();
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        $guardian_types =  GuardianType::get();
        $session = auth()->user()->session;
        $financial_year = auth()->user()->financial_year;
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.national_id',
                    'center_candidate.candidate_no',
                    'center_candidate.email',
                    'center_candidate.phone_number',
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
                    'invoices.amount',
                    'candidate_subject.subject_code',
                    'subjects.subject_name',
                    'option_heads.description',
                    'candidate_subject.subject_option',
                    'addresses.postal_address',
                    'addresses.physical_address',
                    'addresses.district',
                    'addresses.village'
                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('subjects', 'subjects.subject_code', '=', 'candidate_subject.subject_code')
            ->leftJoin('option_heads', 'option_heads.option_code', '=', 'candidate_subject.subject_option')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('invoices', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'invoices.client_id');
                $join->on('candidate_subject.level', '=', 'invoices.level');
                $join->on('candidate_subject.session', '=', 'invoices.session');
                $join->on('candidate_subject.financial_year', '=', 'invoices.financial_year');
            })
            ->leftJoin('addresses', function ($join) {
                $join->on('candidate_subject.national_id', '=', 'addresses.user_id');
                $join->where('addresses.user_type', '=', Candidate::class);
            })
            ->where("center_candidate.candidate_no", '=', $candidate_no)
            ->where("center_candidate.national_id", '=', $national_id)
            ->where("center_candidate.session", '=', $session)
            ->where("center_candidate.financial_year", '=', $financial_year)
            ->first();
        return view('candidate.profile', compact('candidate', 'specialNeeds', 'guardian_types', 'districts'));
    }
    public function showCandidateInfo()
    {
        $national_id = auth()->user()->national_id;
        $candidate_no = auth()->user()->candidate_no;
        $session = auth()->user()->session;
        $financial_year = auth()->user()->financial_year;
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.national_id',
                    'center_candidate.candidate_no',
                    'center_candidate.email',
                    'center_candidate.phone_number',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    'invoices.amount',
                    'candidate_subject.subject_code',
                    'subjects.subject_name',
                    'option_heads.description',
                    'candidate_subject.subject_option',
                    'addresses.postal_address',
                    'addresses.physical_address',
                    'addresses.district',
                    'addresses.village'
                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('subjects', 'subjects.subject_code', '=', 'candidate_subject.subject_code')
            ->leftJoin('option_heads', 'option_heads.option_code', '=', 'candidate_subject.subject_option')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('invoices', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'invoices.client_id');
                $join->on('candidate_subject.level', '=', 'invoices.level');
                $join->on('candidate_subject.session', '=', 'invoices.session');
                $join->on('candidate_subject.financial_year', '=', 'invoices.financial_year');
            })
            ->leftJoin('addresses', function ($join) {
                $join->on('candidate_subject.national_id', '=', 'addresses.user_id');
                $join->where('addresses.user_type', '=', Candidate::class);
            })
            ->where("center_candidate.candidate_no", '=', $candidate_no)
            ->where("center_candidate.national_id", '=', $national_id)
            ->where("center_candidate.session", '=', $session)
            ->where("center_candidate.financial_year", '=', $financial_year);

        $candidate_inforamtion = $candidate->first();




        $specialNeed = CandidateArrangement::where('candidate_no', '=', $candidate_no)->first();

        $guardian = DB::table('guardians')
            ->select(
                [
                    'guardians.national_id',
                    'guardians.guardian_type',
                    'guardians.name',
                    'guardians.surname',
                    'guardians.phone_number',
                    'guardians.email',
                    'addresses.postal_address',
                    'addresses.physical_address',
                    'addresses.district',
                    'addresses.village'
                ],
            )
            ->join('center_candidate', 'center_candidate.candidate_no', '=', 'guardians.candidate')
            ->join('addresses', function ($join) {
                $join->on('guardians.national_id', '=', 'addresses.user_id');
                $join->where('addresses.user_type', '=', Guardian::class);
            })
            ->where("guardians.candidate", '=',  $candidate_inforamtion->candidate_no)
            ->first();

        $paid_fee =  isset($candidate_inforamtion->amount) ?  number_format((float)$candidate_inforamtion->amount, 2, '.', '') : "00.00";

        return response()->json([
            'candidate' =>  $candidate_inforamtion,
            'specialNeed' => $specialNeed,
            'guardian' => $guardian,
            'paid_fee' => $paid_fee
        ]);
    }


    public function showNextOfKin()
    {

        $national_id = auth()->user()->national_id;
        $candidate_no = auth()->user()->candidate_no;
        $specialNeeds = SpecialNeed::get();
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        $guardian_types =  GuardianType::get();
        $candidate = Candidate::where('candidate_no', '=', $candidate_no)->first();
        return view('candidate.nextofkin', compact('candidate', 'specialNeeds', 'guardian_types', 'districts'));
    }

    public function multiformPersonal(Request $request)
    {
        $currentPage = $request->current_page;
        $validationRules = [
            1 => [
                'candidate_no' => ['required'],
                'candidate_email' => ['required', 'email'],
                'candidate_phone_number' => ['required', 'numeric', 'digits:8'],
                'special_need' => ['required'],
                'national_id' => ['required'],
                'candidate_postal_address' => ['required'],
                'candidate_physical_address' => ['required'],
                'candidate_village' => ['required'],
                'candidate_district' => ['required'],
            ],
            2 => [
                'guardian_national_id' => ['required'],
                'guardian_type' => ['required'],
                'guardian_email' => ['required'],
                'guardian_phone_number' => ['required'],
                'guardian_name' => ['required'],
                'guardian_surname' => ['required'],
                'guardian_postal_address' => ['required'],
                'guardian_physical_address' => ['required'],
                'guardian_village' => ['required'],
                'guardian_district' => ['required']
            ],
            3 => [],
        ];
        $validationMassages = [
            1 => [],
            2 => [],
            3 => [],
        ];
        $validator = Validator::make($request->all(), $validationRules[$currentPage],  $validationMassages[$currentPage]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidate_no = $request->candidate_no;
        $session = $request->session;
        $financial_year = $request->financial_year;
        $national_id = $request->national_id;

        switch ($currentPage) {
            case '1':
                CenterCandidate::where('candidate_no', '=', $candidate_no)
                    ->where('national_id', '=',  $national_id)
                    ->where('financial_year', '=',  $financial_year)
                    ->where('session', '=',  $session)
                    ->update([
                        'phone_number' => $request->candidate_phone_number,
                        'email' => $request->candidate_email
                    ]);
                //Address
                Address::updateOrCreate(
                    ['user_id' => $request->national_id, 'user_type' => Candidate::class],
                    [
                        "postal_address" => $request->candidate_postal_address,
                        "physical_address" => $request->candidate_physical_address,
                        "village" => $request->candidate_village,
                        "user_id" => $request->national_id,
                        "user_type" => Candidate::class,
                        "district" => $request->candidate_district,
                    ]
                );
                //  Special Needs

                CandidateArrangement::updateOrCreate(
                    ["candidate_no" => $request->candidate_no],
                    [
                        "candidate_no" => $request->candidate_no,
                        "arrangement_id" => $request->special_need,
                    ]
                );

                return response()->json(['success' => "Successfully saved the record"]);
                break;
            case '2':
                //Gurdian
                Guardian::updateOrCreate(
                    [
                        'candidate' => $candidate_no,
                        'national_id' => $request->guardian_national_id
                    ],
                    [
                        "candidate" => $candidate_no,
                        'national_id' => $request->guardian_national_id,
                        "guardian_type" => $request->guardian_type,
                        "name" => $request->guardian_name,
                        "surname" => $request->guardian_surname,
                        "email" => $request->guardian_email,
                        "phone_number" => $request->guardian_phone_number
                    ]
                );
                // Guardian addresss
                Address::updateOrCreate(
                    ['user_id' => $request->guardian_national_id, 'user_type' => Guardian::class],
                    [
                        'user_id' => $request->guardian_national_id,
                        "postal_address" => $request->guardian_postal_address,
                        "physical_address" => $request->guardian_physical_address,
                        "village" => $request->guardian_village,
                        "user_id" => $request->guardian_national_id,
                        "user_type" => Guardian::class,
                        "district" => $request->guardian_district,
                    ]
                );
                return response()->json(['success' => "Successfully saved the record"]);
                break;
            case '3':
                return response()->json(['success' => "Successfully saved the record"]);
                break;
            default:

                break;
        }
    }
}
