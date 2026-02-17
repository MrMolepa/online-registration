<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Invoice;
use App\Models\Payment as PaymentModel;
use App\Models\SubjectCandidate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Libraries\fpdf\easyTable;
use App\Libraries\fpdf\exFPDF;
use App\Models\Setting;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Storage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// MPESA
use App\Libraries\Mpesa\MpesaApi;
// EcoCash
use App\Libraries\EcoCash\EcoCashApi;
use App\Mail\CandidateInvoiceMail;
use App\Models\Address;
use App\Models\CandidateArrangement;
use App\Models\FeeCandidateHistory;
use App\Models\FeeFine;
use App\Models\FeeStracture;
use App\Models\Guardian;
use App\Models\GuardianType;
use App\Models\Level;
use App\Models\OptionHeader;
use App\Models\SpecialNeed;
use App\Models\Session as ExamsSession;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use League\OAuth2\Client\Provider\Google;


use function PHPUnit\Framework\isEmpty;

class RegistrationController extends Controller
{

    private $candidateInfo = null;

    //
    public function index(Request $request)
    {


        return view('home');
    }


    public function privateCandidate()
    {


        //         $candidate = DB::table('candidate_subject')
        //         ->select(
        //             [
        //                 'center_candidate.id',
        //                 'center_candidate.center_no',
        //                 'center_candidate.candidate_no',
        //                 'center_candidate.session',
        //                 'center_candidate.level',
        //                 'center_candidate.type',
        //                 'center_candidate.subject_number',
        //                 'candidates.candidate_surname',
        //                 'candidates.candidate_other_name',
        //                 'candidates.date_of_birth',
        //                 'candidates.gender',
        //                 'center_candidate.sponser',
        //                 DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
        //    order by candidate_subject.subject_code separator ',') as subjects")
        //             ],
        //         )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
        //         ->join('center_candidate', function ($join) {
        //             $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
        //             $join->on('candidate_subject.level', '=', 'center_candidate.level');
        //             $join->on('candidate_subject.session', '=', 'center_candidate.session');
        //             $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
        //         })->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
        //         ->where("center_candidate.center_no", '=', 'LS500')
        //           ->toSql();
        //           dd( $candidate );


        $levels = Level::where('is_active', '=', 1)->where('private_registration', '=', 1)
            ->get();
        $sessions = ExamsSession::where('is_active', '=', 1)->get();
        $specialNeeds = SpecialNeed::get();
        $guardian_types =  GuardianType::get();
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        return view('registration.home', compact('levels', 'sessions', 'specialNeeds', 'guardian_types', 'districts'));
    }

    // Generate SessionKey
    // This API should always be the first call to be used as it will return the SessionKey
    //  that needs to be used in conjunction with all of the other API calls from the API documentation.
    public function generateSessionKey(Request $request)
    {




        $ecoCashApi = new EcoCashApi();

        $ecoCashApi =   $ecoCashApi->getEcoCashResponse('63966228', 35000);
        dd($ecoCashApi);

        //$this->getNCIRResponse("059259189721");
        $mpesa = new  MpesaApi();

        $mpesa_api = $mpesa->C2BMpesa('59023917', 1000);
        //ob_start();
        $mpesa_api = $mpesa->C2BMpesa('59023917', 1000);
        //$this->getEcoCashResponse($request);
        ob_end_clean();
        //if (!is_null($mpesa_api)) {
        //dd($mpesa_api->body);
        //     // convert json to array
        //     //  create a new collection instance from the array
        //$mpesa_collection = json_decode($mpesa_api->body, true);
        // exit($mpesa_collection);
        // }
    }

    public function autocompleteSearch(Request $request)
    {
        // <!-- search centre -->
        $centers = [];
        if ($request->has('search')) {
            $center_name = $request->get('search');
            $centers = Center::where('center_name', 'LIKE', "{$center_name}%")
                ->whereHas('levels', function ($query) use ($request) {
                    $query->where('levels.id', '=', $request->get('level'));
                })
                ->where('sessions', 'LIKE', "%" . $request->get('session') . "%")
                ->where('status', '=', 1)
                ->limit(5)->get();
            return response()->json($centers);
        }
    }



    private function getNCIRResponse($national_id)
    {


        //  http://197.155.193.219:8087/apiService.svc/GetPersonByID?key=<securityKey>&idNumber=<personId>


        $post = ["key" => "s0zreL1vishn#", "idNumber" => $national_id];
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, "http://197.155.193.219:8087/apiService.svc/GetPersonByID");
        curl_setopt($cURL, CURLOPT_POST, 1);

        curl_setopt($cURL, CURLOPT_HTTPGET, 1);
        // In real life you should use something like:
        curl_setopt(
            $cURL,
            CURLOPT_POSTFIELDS,
            http_build_query($post)
        );

        // Receive server response ...
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($cURL);
        curl_close($cURL);

        if ($response) {
            dd($response, "ok");
            return response()->json(['error' => $response]);
        } else {
            dd($response, "Not Ok");
            return response()->json(['success' => $response]);
        }
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
                $candidate_no = (!empty($request->candidate_no)) ? $request->candidate_no : null;
                $national_id = (!empty($request->national_id)) ? $request->national_id : null;


                $candidate = $this->is_fully_registered($request->alternative, $candidate_no, $national_id, $request->register_sessions);
                $candidateSubjects = explode(',', $candidate->first()->subjects);
                foreach ($candidateSubjects as $subject) {
                    $subject = explode('-', $subject);
                    $candidateRegistedSubjects[] = $subject[1];
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
                    $is_subjects = true;
                    $subjectsHTML = "";
                    if ($key % 2 == 0) {
                        if (in_array($subject->subject_code, $doubleOptionsSubjects)) {
                            foreach ($optionHeaders as $optionHeader) {
                                $disableSubject = in_array($subject->subject_code, $candidateRegistedSubjects) ? "disabled" : "";
                                $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                                    <input type='checkbox' name='subject[]'$disableSubject class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$optionHeader->option_code,$subject->subject_name' id='$optionHeader->option_code-$subject->subject_code'>
                                                    <label class='custom-control-label' for='$optionHeader->option_code-$subject->subject_code'>$subject->subject_name- $optionHeader->description</label>
                                                </li>";
                            }
                        } else {
                            $disableSubject = in_array($subject->subject_code, $candidateRegistedSubjects) ? "disabled" : "";
                            $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                                <input type='checkbox' name='subject[]'$disableSubject class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$subject->option_code,$subject->subject_name' id='$subject->option_code-$subject->subject_code'>
                                                <label class='custom-control-label' for='$subject->option_code-$subject->subject_code'>$subject->subject_name</label>
                                            </li>";
                        }
                        $leftSubject .= $subjectsHTML;
                    } else {
                        if (in_array($subject->subject_code, $doubleOptionsSubjects)) {

                            foreach ($optionHeaders as $optionHeader) {
                                $disableSubject = in_array($subject->subject_code, $candidateRegistedSubjects) ? "disabled" : "";
                                $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                                <input type='checkbox' name='subject[]' $disableSubject class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$optionHeader->option_code,$subject->subject_name' id='$optionHeader->option_code-$subject->subject_code'>
                                                <label class='custom-control-label' for='$optionHeader->option_code-$subject->subject_code'>$subject->subject_name- $optionHeader->description</label>
                                            </li>";
                            }
                        } else {

                            $disableSubject = in_array($subject->subject_code, $candidateRegistedSubjects) ? "disabled" : "";
                            $subjectsHTML .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                            <input type='checkbox' name='subject[]' $disableSubject class='custom-control-input subject subj_$subject->subject_code' value='$subject->subject_code,$subject->option_code,$subject->subject_name' id='$subject->option_code-$subject->subject_code'>
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

                return response()->json(['subjectsHTML' =>  $html, 'doubleOptionsSubjects' => $doubleOptionsSubjects, 'disableSubject' => $request->all()]);
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
    // All steps for registration
    public function register(Request $request)
    {

        if ($request->has("is_candidate_new")) {
            $cadidatete_reg = $request->get("is_candidate_new");
            $html = "";
            $is_candidate_new = false;
            $districts = Center::groupBy('district_code')
                ->whereNotNull('district_code')->get();
            $districtsHtml = "";
            $specialNeeds = SpecialNeed::get();
            $specialNeedsHtml = "";
            foreach ($specialNeeds  as $specialNeed) {
                $specialNeedsHtml .= "<option value='$specialNeed->id'> $specialNeed->name</option>";
            }
            foreach ($districts  as  $district) {
                $districtsHtml .= "<option value='$district->district'> $district->district</option>";
            }
            if ($cadidatete_reg == "existing-candidate") {
                $is_candidate_new = false;
                $html = "
                   <div class='progress'>
                        <div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' aria-valuemin='0' aria-valuemax='100'></div>
                    </div>
                    <section id='personal-section'>
                        <div class='registered-info'>
                        </div>
                        <div class='form-group'>
                            <h2 class='fs-title'>Existing Candidate</h2>
                            <label for='candidate_no'>Candidate
                                number</label>
                            <input type='text'
                                class='form-control search_candidate_input'
                                placeholder='Enter Candidate Number'
                                name='candidate_no' id='candidate_no'>
                        </div>
                        <div class='form-group'>
                            <label for='national_id'>National
                                ID</label>
                            <input type='text' class='form-control'
                                placeholder='Enter National ID Number'
                                name='national_id' id='national_id'>
                        </div>
                        <div class='form-row'>
                            <div class='form-group col-md-6'>
                                <label for='candidate_surname'>Surname</label>
                                <input type='text' class='form-control'
                                    name='candidate_surname'
                                    placeholder='Enter Surname'
                                    id='candidate_surname' >
                            </div>
                            <div class='form-group col-md-6'>
                                <label for='candidate_other_name'>Other
                                    name</label>
                                <input type='text' class='form-control'
                                    name='candidate_other_name'
                                    placeholder='Enter other names'
                                    id='candidate_other_name' >
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='form-group col-md-6'>
                                <label for='date_of_birth'>Date of birth</label>
                                <input type='date' class='form-control'
                                    id='date_of_birth' name='date_of_birth'>
                            </div>
                            <div class='form-group col-md-6'>
                                <label>Gender</label>
                                <select name='gender' class='form-control' id='special_need'>
                                <option value=''> Please Select gender</option>
                                   <option value='F'>Female</option>
                                   <option value='M'>Male </option>
                               </select>
                            </div>
                        </div>
                            <input type='button' name='varify-infomation' class='varify-infomation next_personal action-btn' value='verify' />
                    </section>
                    <section id='verifications-section'>
                        <h2 class='fs-title'>Verification </h2>
                        <div class='form-row'>
                            <div class='form-group col-md-12 '>
                                <label for='email' class='col-lg-4'>Email
                                    address</label>
                                <input type='text' name='email'
                                    class='form-control' id='email'
                                    placeholder='*Email address'>

                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='form-group col-md-12'>
                                <label for='phone_number'
                                    class='col-lg-4'>Phone number</label>
                                <input type='text' name='phone_number'
                                    class='form-control' id='phone_number'
                                    placeholder='*Phone number'>
                            </div>
                        </div>
                             <input type='button' name='next_personal' class='next_personal action-btn' value='next' />
                            <input type='button' name='previous_personal' class='previous_personal action-btn ' value='previous' />
                    </section>
                    <section id='addresss'>
                        <div class='form-group'>
                        <h2 class='fs-title'>Addess</h2>
                         <div class='row'>
                                <div class='form-group col-6'>
                                    <label
                                        for='candidate_postal_address'>Postal
                                        address </label>
                                    <input type='text'
                                        class='form-control'
                                        id='candidate_postal_address'
                                        name='candidate_postal_address'
                                        placeholder='P.O. Box 507'>

                                </div>
                                <div class='form-group col-6'>
                                    <label
                                        for='candidate_physical_address'>Physical
                                        address</label>
                                    <input type='text'
                                        class='form-control'
                                        id='candidate_physical_address'
                                        name='candidate_physical_address'
                                        placeholder='Selakhapane'>
                                </div>

                                <div class='form-group col-6'>
                                    <label
                                        for='candidate_village'>Village</label>
                                    <input type='text'
                                        class='form-control'
                                        id='candidate_village'
                                        name='candidate_village'
                                        placeholder='Khubetsoana'>
                                </div>
                                <div class='form-group col-6'>
                                    <label
                                        for='candidate_district'>District</label>
                                    <select
                                        class='form-control'
                                        name='candidate_district'
                                        id='candidate_district'>
                                        <option value=''>Select district</option>
                                            $districtsHtml
                                    </select>
                                </div>
                            </div>
                        </div>
                            <input type='button' name='next_personal' class='next_personal action-btn' value='next' />
                            <input type='button' name='previous_personal' class='previous_personal action-btn ' value='previous' />
                    </section>
                    <section id='special-section'>
                        <div class='form-group'>
                        <h2 class='fs-title'>Special Needs</h2>
                        <select name='special_need' class='required form-control'
                            id='special_need'>
                            <option value=''>Please Select Special Need</option>
                               $specialNeedsHtml
                            </select>
                        </div>
                            <input type='button' name='next_guardian' class='next_personal action-btn' value='next' />
                            <input type='button' name='previous_personal' class='previous_personal action-btn ' value='previous' />

                    </section>";
            } else {
                $is_candidate_new = true;
                $html = "
                <div class='progress'>
                     <div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' aria-valuemin='0' aria-valuemax='100'></div>
                 </div>

                 <section id='personal-section'>
                    <div class='registered-info'>
                    </div>
                     <div class='form-group'>
                         <label for='national_id'>National
                             ID</label>
                         <input type='text' class='form-control'
                             placeholder='Enter national ID number'
                             name='national_id' id='national_id'>
                     </div>
                     <div class='form-row'>
                         <div class='form-group col-md-6'>
                             <label for='candidate_surname'>Surname</label>
                             <input type='text' class='form-control'
                                 name='candidate_surname'
                                 placeholder='Enter surname'
                                 id='candidate_surname' >
                         </div>
                         <div class='form-group col-md-6'>
                             <label for='candidate_other_name'>Other
                                 name(s)</label>
                             <input type='text' class='form-control'
                                 name='candidate_other_name'
                                 placeholder='Enter other name(s)'
                                 id='candidate_other_name' >
                         </div>
                     </div>
                     <div class='form-row'>
                         <div class='form-group col-md-6'>
                             <label for='date_of_birth'>Date of birth</label>
                             <input type='date' class='form-control'
                                 id='date_of_birth' name='date_of_birth'>
                         </div>
                         <div class='form-group col-md-6'>
                             <label>Gender</label>
                             <select name='gender' class='form-control' id='special_need'>
                             <option value=''> Select gender</option>
                                <option value='F'>Female</option>
                                <option value='M'>Male </option>
                            </select>
                         </div>
                     </div>
                         <input type='button' name='varify-infomation' class='varify-infomation next_personal action-btn' value='verify' />
                 </section>
                 <section id='verifications-section'>
                     <h2 class='fs-title'>Verification </h2>
                     <div class='form-row'>
                         <div class='form-group col-md-12 '>
                             <label for='email' class='col-lg-4'>Email
                                 address</label>
                             <input type='text' name='email'
                                 class='form-control' id='email'
                                 placeholder='*Email address'>

                         </div>
                     </div>
                     <div class='form-row'>
                         <div class='form-group col-md-12'>
                             <label for='phone_number'
                                 class='col-lg-4'>Phone number</label>
                             <input type='text' name='phone_number'
                                 class='form-control' id='phone_number'
                                 placeholder='*Phone number'>
                         </div>
                     </div>
                          <input type='button' name='next_personal' class='next_personal action-btn' value='next' />
                         <input type='button' name='previous_personal' class='previous_personal action-btn ' value='previous' />
                 </section>
                 <section id='addresss'>
                     <div class='form-group'>
                     <h2 class='fs-title'>Addess</h2>
                      <div class='row'>
                             <div class='form-group col-6'>
                                 <label
                                     for='candidate_postal_address'>Postal
                                     Address </label>
                                 <input type='text'
                                     class='form-control'
                                     id='candidate_postal_address'
                                     name='candidate_postal_address'
                                     placeholder='P.O.Box 507'>

                             </div>
                             <div class='form-group col-6'>
                                 <label
                                     for='candidate_physical_address'>Physical
                                     Address</label>
                                 <input type='text'
                                     class='form-control'
                                     id='candidate_physical_address'
                                     name='candidate_physical_address'
                                     placeholder='Selakhapane'>
                             </div>

                             <div class='form-group col-6'>
                                 <label
                                     for='candidate_village'>Village</label>
                                 <input type='text'
                                     class='form-control'
                                     id='candidate_village'
                                     name='candidate_village'
                                     placeholder='Khubetsoana'>
                             </div>
                             <div class='form-group col-6'>
                                 <label
                                     for='candidate_district'>District</label>
                                 <select
                                     class='form-control'
                                     name='candidate_district'
                                     id='candidate_district'>
                                     <option value=''>Select district</option>
                                         $districtsHtml
                                 </select>
                             </div>
                         </div>
                     </div>
                         <input type='button' name='next_personal' class='next_personal action-btn' value='next' />
                         <input type='button' name='previous_personal' class='previous_personal action-btn ' value='previous' />
                 </section>
                 <section id='special-section'>
                     <div class='form-group'>
                     <h2 class='fs-title'>Special needs</h2>
                     <select name='special_need' class='required form-control'
                         id='special_need'>
                         <option value=''>Select Special need</option>
                            $specialNeedsHtml
                         </select>
                     </div>
                         <input type='button' name='next_guardian' class='next_personal action-btn' value='next' />
                         <input type='button' name='previous_personal' class='previous_personal action-btn ' value='previous' />

                 </section>";
            }
            return response()->json([
                'is_candidate_new' =>  $is_candidate_new,
                'html' => $html,

            ]);


            $is_candidate_new = false;
        }
        // Fees
        if ($request->has("fee_stracture")) {
            $validator = Validator::make($request->all(), [
                'session' => 'required',
                'level' => 'required',
                'session_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
            $session = ExamsSession::find($request->session_id);
            $level = $request->level;
            $financial_year = $session->financial_year;
            $subjects_fee = DB::table('fee_groups')
                ->select(
                    'fee_groups.id',
                    'fee_types.fee_name',
                    'fee_group_details.subject_code',
                    'fee_group_details.amount'
                )
                ->join('fee_group_details', 'fee_group_details.fee_group_id', '=', 'fee_groups.id')
                ->join('fee_types', 'fee_types.id', '=', 'fee_group_details.fee_type_id')
                ->join('sessions', 'sessions.id', '=', 'fee_groups.session_id')
                ->join('levels', 'levels.id', '=', 'fee_groups.level_id')
                ->where('sessions.id', '=', $request->session_id)
                ->where('sessions.financial_year', '=', $financial_year)
                ->where('fee_groups.candidate_type', '=',  3)
                ->where('levels.level', '=', $level)
                ->where('subject_code', '!=', '-')
                ->get();

            $administrative_fee = DB::table('fee_groups')
                ->select(
                    'fee_group_details.amount'
                )
                ->join('fee_group_details', 'fee_group_details.fee_group_id', '=', 'fee_groups.id')
                ->join('fee_types', 'fee_types.id', '=', 'fee_group_details.fee_type_id')
                ->join('sessions', 'sessions.id', '=', 'fee_groups.session_id')
                ->join('levels', 'levels.id', '=', 'fee_groups.level_id')
                ->where('sessions.id', '=', $request->session_id)
                ->where('sessions.financial_year', '=', $financial_year)
                ->where('fee_groups.candidate_type', '=',  3)
                ->where('levels.level', '=', $level)
                ->where('subject_code', '=', '-')
                ->get()->sum('amount');

            if ($request->has("subject_addition")) {
                $administrative_fee = 0;
                $administratives = collect([]);
            }

            $total_fine = 0;
            $groupId =  $subjects_fee->first()->id;
            $fine = FeeFine::where('fee_group_id', '=', $groupId)
                ->where('start_date', '<=',   date('Y-m-d'))
                ->where('end_date', '>=',   date('Y-m-d'))
                ->first();
            if ($fine) {
                $total_fine = $fine->fine_value;
            }
            $administrative_fee += $total_fine;

            return response()->json([
                'administrative_fee' =>   $administrative_fee,
                'total_fine' => $total_fine,
                'fee_group_id' => $groupId,
                'subjects_fee' => $subjects_fee->pluck('amount', 'subject_code')->toArray(),
            ]);
        }
        if ($request->has('next-billing')) {

            $validator = Validator::make($request->all(), [
                'candidate_no' => [
                    'required_if:alternative,==,existing-candidate',
                    'exists:candidates,candidate_no'
                ],
                'national_id' => 'required',
                'candidate_surname' => 'required',
                'candidate_other_name' => 'required',
                'date_of_birth' => 'required',
                'gender' => 'required',
                'payment' => 'required',
                'session' => 'required',
                'level' => 'required',
                'center' => 'required',
                'number_of_subjects' => 'required|numeric',
                'phone_number' => [Rule::requiredIf(function () use ($request) {
                    return !$request->has('register_sessions');
                })],
                'email' => [Rule::requiredIf(function () use ($request) {
                    return !$request->has('register_sessions');
                })],
                'total_amount' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $candidate = Candidate::where('candidate_no', '=', isset($request->candidate_no) ? $request->candidate_no : "122")->first();
            if (!$candidate) {
                $candidate = (array)$candidate;
                $candidate['national_id'] = $request->national_id;
                $candidate['candidate_surname'] = $request->candidate_surname;
                $candidate['candidate_other_name'] = $request->candidate_other_name;
                $candidate['date_of_birth'] = $request->date_of_birth;
                $candidate['gender'] = $request->gender;
                $candidate = collect([(object)$candidate])->first();
            }
            // All Subjects
            $subjects =   $request->has('subject') ?   $request->subject : array();

            // Total Amount
            $total_amount = 0;
            // Fees
            $session = ExamsSession::find($request->session_id);
            $level = strtolower($request->level);
            $financial_year = $session->financial_year;

            $subjects_fee = DB::table('fee_groups')
                ->select(
                    'fee_groups.id',
                    'fee_types.fee_name',
                    'fee_group_details.subject_code',
                    'fee_group_details.amount'
                )
                ->join('fee_group_details', 'fee_group_details.fee_group_id', '=', 'fee_groups.id')
                ->join('fee_types', 'fee_types.id', '=', 'fee_group_details.fee_type_id')
                ->join('sessions', 'sessions.id', '=', 'fee_groups.session_id')
                ->join('levels', 'levels.id', '=', 'fee_groups.level_id')
                ->where('sessions.id', '=', $request->session_id)
                ->where('sessions.financial_year', '=', $financial_year)
                ->where('fee_groups.candidate_type', '=',  3)
                ->where('levels.level', '=', $level)
                ->where('subject_code', '!=', '-')
                ->get();

            $administratives = DB::table('fee_groups')
                ->select(
                    'fee_types.fee_name',
                    'fee_group_details.amount'
                )
                ->join('fee_group_details', 'fee_group_details.fee_group_id', '=', 'fee_groups.id')
                ->join('fee_types', 'fee_types.id', '=', 'fee_group_details.fee_type_id')
                ->join('sessions', 'sessions.id', '=', 'fee_groups.session_id')
                ->join('levels', 'levels.id', '=', 'fee_groups.level_id')
                ->where('sessions.id', '=', $request->session_id)
                ->where('sessions.financial_year', '=', $financial_year)
                ->where('fee_groups.candidate_type', '=',  3)
                ->where('levels.level', '=', $level)
                ->where('subject_code', '=', '-')
                ->get();

            $administrative_fee = $administratives->sum('amount');

            if ($request->has("appending-subjects")) {
                $administrative_fee = 0;
                $administratives = collect([]);
            }

            $total_fine = 0;
            $groupId =  $subjects_fee->first()->id;
            $fine = FeeFine::where('fee_group_id', '=', $groupId)
                ->where('start_date', '<=',   date('Y-m-d'))
                ->where('end_date', '>=',   date('Y-m-d'))
                ->first();
            if ($fine) {
                $total_fine = $fine->fine_value;
            }
            $administrative_fee += $total_fine;
            $total_subject = count($subjects);
            $subjects_fee = $subjects_fee->pluck('amount', 'subject_code')->toArray();
            $total_amount =  $administrative_fee;
            $center = Center::where('center_no', '=', $request->center)->first();
            $candidate_no = isset($candidate->candidate_no) ? str_pad($candidate->candidate_no, 9, '0', STR_PAD_LEFT) : "";
            $output = "";
            $output .= "<div class='col-md-10'>
                            <div class='card'>
                                <div class='upper p-4'>
                                    <div class='d-flex justify-content-between'>

                                            <div>
                                                <span class='font-weight-bold d-block'>National ID : $request->national_id</span>
                                                <span class='text-primary font-weight-bold'>Candidate Number :$candidate_no </span>
                                                <span class='font-weight-bold d-block'>Surname : $candidate->candidate_surname</span>
                                                <span class='font-weight-bold d-block'>Other Name : $candidate->candidate_other_name</span>
                                                <span class='font-weight-bold d-block'>Date of Birth : $candidate->date_of_birth</span>
                                                <hr>
                                                <span class='font-weight-bold d-block'>Centre: $center->center_name</span>
                                                <span class='font-weight-bold d-block'>Session: {$request->session} " . date('Y') . "</span>
                                                <span class='font-weight-bold d-block'>Level : {$request->level}</span>
                                                <span class='font-weight-bold d-block'>Registration Date : " . date('Y-m-d') . "</span>
                                            </div>
                                            <div>
                                                <img src='" . asset('assets/images/logo.png') . "' width='100px' alt=''>
                                            </div>
                                    </div>
                                    <hr>
                                    ";
            if ($request->has('subject')) {
                for ($i = 0; $i < sizeof($subjects); $i++) {
                    $code = explode(',', $subjects[$i]);
                    $subject_code = $code[0];
                    $total_amount  +=  $subjects_fee[$subject_code];

                    $output .= "<div class='transaction mt-2'>
                                                <div class='d-flex justify-content-between'>
                                                    <div class='d-flex flex-row align-items-center'>
                                                    <i class='fa fa-check-circle-o'></i>
                                                    <span class='ml-2'>{$subjects[$i]}</span>
                                                    </div> <span class='font-weight-bold'></span>
                                                </div>
                                            </div>";
                }
            }
            $output .= " <hr> ";
            foreach ($administratives as $administrative) {
                $output .= "<div class='transaction mt-2 '>
                                <div class='d-flex justify-content-between   '>
                                    <div class='d-flex flex-row align-items-center '>
                                        <i class='fa fa-check-circle-o'></i> <span class='ml-2'></span>
                                    </div> <span class='font-weight-bold registration-fee'></span>
                                </div>
                            </div>";
            }
            $output .= "<div class='lower bg-primary p-4 text-white d-flex justify-content-between'>
                            <div class='d-flex flex-column'>
                                <span>Total Cost including bank charges</span>
                            </div>
                            <h3>LSL  $total_amount,00</h3>
                        </div>
                    </div>

                </div>";

            return response()->json(['html' => $output, 'total_amount' => $total_amount, 'subject_number' => $total_subject]);
        }
        // Increase Subjects
        if ($request->has('appending-subjects')) {
            $candidate_no = $request->candidateNo;
            $candidate = DB::table('candidate_subject')
                ->select(
                    'center_candidate.candidate_no',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.type',
                    'center_candidate.level',
                    'center_candidate.center_no',
                    'center_candidate.subject_number',
                    'center_candidate.sponser',
                    'center_candidate.session',
                    'center_candidate.email',
                    'center_candidate.phone_number',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
            order by candidate_subject.subject_code separator ',') as subjects")
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                ->join('center_candidate', 'center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
                ->where('center_candidate.candidate_no', '=', $candidate_no)
                ->groupBy('center_candidate.candidate_no')
                ->first();
            $center_no =  $candidate->center_no;
            $center = Center::where('center_no', '=',  $center_no)->first();
            return response()->json(['result' =>  $candidate, 'centre' => $center]);
        }
    }
    private function is_fully_registered($alternative, $candidate_no = null, $national_id = null, $session = null)
    {
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.center_no',
                    'centers.center_name',
                    'subjects.subject_name',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.financial_year',
                    'center_candidate.email',
                    'center_candidate.phone_number',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    DB::raw("group_concat(DISTINCT concat(subjects.subject_name,'-',candidate_subject.subject_code,'-',option_heads.description)
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
            ->join('fee_candidate_histories', function ($join) {
                $join->on('center_candidate.id', '=', 'fee_candidate_histories.candidate_id');
            })
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->where('center_candidate.financial_year', '=', date('Y') . '-' . (date('Y') + 1))
            ->where('fee_candidate_histories.status', '=', 1);



        if ($alternative == "existing-candidate") {
            $candidate = ($candidate_no == null ?  $candidate : $candidate->where('center_candidate.candidate_no', '=', $candidate_no));
            $candidate = ($session == null ?  $candidate : $candidate->where('center_candidate.session', '=', $session));
            $candidate = $candidate->get();
        } else {
            $candidate = ($session == null ?  $candidate : $candidate->where('center_candidate.session', '=', $session));

            $candidate = ($national_id == null ?  $candidate : $candidate->where('center_candidate.national_id', '=', $national_id));
            $candidate = $candidate->get();
        }

        if ($candidate->count() > 0) {
            return  $candidate;
        } else {
            return false;
        }
    }

    private function is_partially_registered($alternative, $candidate_no = null, $national_id = null, $session = null)
    {

        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.center_no',
                    'center_candidate.candidate_no',
                    'center_candidate.session',
                    'center_candidate.national_id',
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
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.national_id', '=', 'center_candidate.national_id');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->where('center_candidate.financial_year', '=', date('Y') . '-' . (date('Y') + 1));

        if ($alternative == "existing-candidate") {
            $candidate = ($candidate_no === null ?  $candidate : $candidate->where('center_candidate.candidate_no', '=', $candidate_no));
            $candidate = ($session === null ?  $candidate : $candidate->where('center_candidate.session', '=', $session));
            $candidate = $candidate->get();
        } else {
            $candidate = ($session === null ?  $candidate : $candidate->where('center_candidate.session', '=', $session));
            $candidate = ($national_id === null ?  $candidate : $candidate->where('center_candidate.national_id', '=', $national_id));
            $candidate = $candidate->get();
        }


        if ($candidate->count() > 0) {
            return  $candidate;
        } else {
            return false;
        }
    }

    public function candidateSubjects(Request $request)
    {
        $html = "";
        $registered = false;
        $candidate_no = !empty($request->input('candidate_no')) ? $request->candidate_no : null;

        if ($request->session == 'new-session') {
            // $guardian = DB::table('addresses')
            //     ->select('user_id', 'postal_address', 'physical_address', 'district_code', 'district','village', 'candidate', 'national_id', 'guardian_type', 'name', 'surname', 'phone_number', 'email')
            //     ->join('guardians', 'guardians.national_id', '=', 'addresses.user_id')
            //      ->where('user_type','=',Guardian::class)
            //      ->where('candidate','=',$request->national_id)
            //     ->first();

            $html = "";
            $candidate  = $this->is_fully_registered($request->alternative, $candidate_no, $request->national_id, null)->first();
            return response()->json(['html' =>  $html, 'registered' => $registered, 'candidate' =>  $candidate]);
        }
        if ($this->is_fully_registered($request->alternative, $candidate_no, $request->national_id, $request->session)) {
            $candidate  = $this->is_fully_registered($request->alternative, $candidate_no, $request->national_id, $request->session)->first();
            $timetable = "";
            if (is_publised($candidate->level, $candidate->session)) {
                $href = route('registeration.printtimetable', [
                    'centre_no' => $candidate->center_no,
                    'candidate_no' => $candidate->candidate_no,
                    'level' => $candidate->level,
                    'session' => $candidate->session,
                    'download' => 1
                ]);

                $hrefSend = route('registeration.printtimetable', [
                    'centre_no' => $candidate->center_no,
                    'candidate_no' => $candidate->candidate_no,
                    'level' => $candidate->level,
                    'session' => $candidate->session,
                    'download' => 1,
                    'send' => 1
                ]);
                $timetable = "<a href='$href'class='btn btn-primary'><i class='fa fa-download '></i> Download</a>
                <a href='$hrefSend'class='btn btn-primary'><i class='fa fa-envelope' aria-hidden='true'></i> Email</a>";
            } else {
                $timetable = "<p class='text-danger'>You will get your timetable once it has been officially published. </p>";
            }

            $leftSubject = "<div class='col-md-6'>
                              <ul class='list-group'>";
            $closingTag = "</ul>
                              </div>";
            $rightSubject = "<div class='col-md-6'>
                             <ul class='list-group'>";
            $subjects = explode(',', $candidate->subjects);
            foreach ($subjects  as $key => $subject) {
                $subject = explode('-', $subject);
                $subject_name = $subject[0];
                $subject_code = $subject[1];
                $core_exetended = in_array($subject_code, ['0178', '0181']) ? $subject[2] : "";
                if ($key % 2 == 0) {
                    $leftSubject .= "<li class='list-group-item text-left'>$subject_name  $subject_code $core_exetended</li>";
                } else {
                    $rightSubject .= "<li class='list-group-item text-left'>$subject_name  $subject_code $core_exetended</li>";
                }
            }
            $html .= "$leftSubject $closingTag  $rightSubject $closingTag ";
            $html .= "<div class='col-md-12 mt-2'>Timetable : $timetable </div>
                <div class='col-md-12 tacbox'>
                    <input type='checkbox'  name='appending-subjects' value='1' id='appending-subjects'>
                    <label for='appending-subjects'>Would you like to add subjects.</label>
                </div>
            ";
            $registered = true;
        }
        return response()->json([
            'html' =>  $html,
            'registered' => $registered,
            'center' => $candidate,
        ]);
    }

    public function paymentTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_no' => ['required_if:alternative,==,existing-candidate', 'exists:candidates,candidate_no'],
            'payment' => 'required',
            'session' => 'required',
            'national_id' => 'required',
            'level' => 'required',
            'center' => 'required',
            'number_of_subjects' => 'required|numeric',
            'phone_number' => [Rule::requiredIf(function () use ($request) {
                return !$request->has('register_sessions');
            })],
            'email' => [Rule::requiredIf(function () use ($request) {
                return !$request->has('register_sessions');
            })],
            'total_amount' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidate_no  =  $request->candidate_no;
        $payment = $request->payment;
        $centreNo =  $request->center;
        $session = $request->session;
        $national_id = $request->national_id;
        $level =  $request->level;
        $financial_year = date('Y') . '-' . (date('Y') + 1);
        $fee_group_id = $request->fee_group_id;
        $fine = $request->fine;
        $info = [];

        switch ($payment) {
            case 'CreditCard':
                // payment
                $ecom_ConsumerOrderID =  $request->Ecom_ConsumerOrderID;
                $amount =  $request->Lite_Order_Amount;
                $amount =    $amount / 100;
                $status = 0;
                if ($request->Lite_Payment_Card_Status == "0") {
                    // Register Candidate
                    $candidate = $this->registerCandidate($request, $financial_year, $candidate_no);
                    $candidate_id = $candidate->id;
                    $candidate_no = $candidate->candidate_no;
                    FeeCandidateHistory::create([
                        'candidate_id' => $candidate_id,
                        'reference_no' => $ecom_ConsumerOrderID,
                        'amount' =>    $amount,
                        'fine' =>  $fine,
                        'fee_group_id' => $fee_group_id,
                        'attachment' => '',
                        'pay_via' => 2,
                        'collect_by' => 'online',
                        'remarks' => "LITE :  $ecom_ConsumerOrderID   Candidate number:  $candidate_no ",
                        'status' => 1
                    ]);
                    $status = 1;
                    $info = [
                        'centreNo' => $centreNo,
                        'candidate_no' => $candidate_no,
                        'level' => $level,
                        'session' => $session,
                    ];
                    $this->sendConfirmation($candidate->national_id,  $candidate->candidate_no,  $candidate->session,  $candidate->financial_year);
                    return response()->json(['status' => $status, 'message' => $request->Lite_Result_Description, 'output' =>  $info, 'publised' => is_publised($level, $session)]);
                } else {
                    return response()->json(['status' => $status, 'message' => $request->Lite_Result_Description, 'output' =>  $info]);
                }
                break;
            case 'CashDeposit':
                // payment
                $validator = Validator::make($request->all(), [
                    'bank_confirmation' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                if ($request->file()) {
                    $fileName =  $candidate_no  . '-' . time() . '-' . $request->bank_confirmation->getClientOriginalName();
                    $filePath = $request->bank_confirmation->storeAs('bankStatement/' .  $candidate_no, $fileName, 'public');
                    DB::table('candidate_confirmation')->insert(
                        array(
                            'candidate_no' => $candidate_no,
                            'candidate_info'   =>   json_encode($request->all()),
                            'bank_confirmation'   => $fileName,
                            'bank_confirmation_path'   =>  '/storage/' . $filePath,
                        )
                    );
                    $info = [
                        'centreNo' => $centreNo,
                        'candidate_no' => $candidate_no
                    ];

                    return response()->json(['status' => 1, 'output' =>  $info, 'publised' => is_publised($level, $session)]);
                }


                break;
            case 'VclMpesa':
                // payment
                $validator = Validator::make($request->all(), [
                    'mpesa_mobile' => 'required|digits:8'
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                ob_start();
                $mpesa = new  MpesaApi();
                $mpesa_api = $mpesa->C2BMpesa($request->mpesa_mobile, (int)$request->total_amount);
                $status = 0;
                ob_end_clean();
                if (!is_null($mpesa_api)) {
                    // convert json to array
                    $mpesa_body = json_decode($mpesa_api->body, true);
                    if ($mpesa_body['output_ResponseCode'] == 'INS-0') {
                        // Register Candidate
                        $candidate = $this->registerCandidate($request, $financial_year, $candidate_no);
                        $candidate_id = $candidate->id;
                        $candidate_no = $candidate->candidate_no;
                        $thirdPartyConversationID =   $mpesa_body['output_ThirdPartyConversationID'];
                        $amount =  $request->total_amount;
                        FeeCandidateHistory::create([
                            'candidate_id' => $candidate_id,
                            'reference_no' => $thirdPartyConversationID,
                            'amount' =>    $amount,
                            'fine' =>  $fine,
                            'fee_group_id' => $fee_group_id,
                            'attachment' => '',
                            'pay_via' => 1,
                            'collect_by' => 'online',
                            'remarks' => "MPESA :  $request->mpesa_mobile  Candidate number:  $candidate_no ",
                            'status' => 1
                        ]);
                        $status = 1;

                        $info = [
                            'centreNo' => $centreNo,
                            'candidate_no' => $candidate_no,
                            'level' => $level,
                            'session' => $session,
                        ];
                        $this->sendConfirmation($candidate->national_id,  $candidate->candidate_no,  $candidate->session,  $candidate->financial_year);
                        exit(json_encode(['status' => $status, 'message' => $mpesa_body['output_ResponseDesc'], 'output' =>  $info, 'publised' => is_publised($level, $session)]));
                    } else {
                        exit(json_encode(['errors' => array('mpesa_mobile' => array($mpesa_body['output_ResponseDesc']))]));
                    }
                } else {
                    exit(json_encode(['errors' => array('mpesa_mobile' => array('Transaction Failed'))]));
                }
                break;
            case 'EcoCash':
                // payment
                $validator = Validator::make($request->all(), [
                    'ecocash_mobile' => 'required|digits:8',
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                ob_start();
                $ecoCashApi = new EcoCashApi();
                $ecoCashApi =   $ecoCashApi->getEcoCashResponse($request->ecocash_mobile,  $request->total_amount, $candidate_no, 'Exams Fees');
                ob_end_clean();
                $status = 0;
                if ($ecoCashApi) {
                    // convert json to array
                    if (!isset($ecoCashApi->status) && isset($ecoCashApi->extra_data) &&  isset($ecoCashApi->request_id)) {
                        // Register Candidate
                        $candidate = $this->registerCandidate($request, $financial_year, $candidate_no);
                        $candidate_id = $candidate->id;
                        $candidate_no = $candidate->candidate_no;

                        $thirdPartyConversationID =   $ecoCashApi->request_id;
                        $amount =  $request->total_amount;
                        FeeCandidateHistory::firstOrCreate([
                            'candidate_id' => $candidate_id,
                            'reference_no' => $thirdPartyConversationID,
                            'amount' =>    $amount,
                            'fee_group_id' => $fee_group_id,
                        ], [
                            'candidate_id' => $candidate_id,
                            'reference_no' => $thirdPartyConversationID,
                            'amount' =>    $amount,
                            'fine' =>  $fine,
                            'fee_group_id' => $fee_group_id,
                            'attachment' => '',
                            'pay_via' => 3,
                            'collect_by' => 'online',
                            'remarks' => "EcoCash :  $request->ecocash_mobile  Candidate number:  $candidate_no ",
                            'status' => 1
                        ]);
                        $status = 1;
                        // Send  Email
                        $this->sendConfirmation($national_id,   $candidate_no, $session,  $financial_year);
                        $info = [
                            'centreNo' => $request->center_no,
                            'candidate_no' =>  $candidate_no,
                            'level' => $level,
                            'session' => $session,
                        ];
                        exit(json_encode(['status' => $status, 'message' => $ecoCashApi->message, 'output' =>  $info, 'publised' => is_publised($level, $session)]));
                    } else {
                        return response()->json(['errors' => ['ecocash_mobile' =>  array($ecoCashApi->message)]]);
                    }
                } else {
                    return response()->json(['errors' => ['ecocash_mobile' => array('Transaction Failed')]]);
                }
                break;
            default:
                break;
        }
    }
    // Upload proof of payments
    public function paymentBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_no' => 'required',
            'bank_confirmation' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $candidateNo = $request->candidateNo;
        $fileName =  $candidateNo  . '-' . time() . '-' . $request->bank_confirmation->getClientOriginalName();
        $filePath = $request->bank_confirmation->storeAs('bankStatement/' .  $candidateNo, $fileName, 'public');
        return response()->json(['status' => 1,]);
    }

    public function registerCandidate(Request $request, $financial_year, $candidate_no)
    {
        try {
            if (!$request->has("appending-subjects") && !$request->has("register_sessions")) {
                if (!$request->has('candidate_no')) {
                    $new_candidate = Candidate::whereDate('date_of_birth', date("Y-m-d", strtotime($request->date_of_birth)))
                        ->where('gender', '=', strtoupper($request->gender))
                        ->where('candidate_surname', '=', strtoupper($request->candidate_surname))->first();
                    $candidate_no = (!$new_candidate) ? getNextCandidateNumber() : $new_candidate->candidate_no;
                    $request->merge(['candidate_no' => $candidate_no]);
                    // Assign Candidate Numberber
                    if (!$new_candidate) {
                        Candidate::create([
                            'candidate_no' => $request->candidate_no,
                            'national_id' => $request->national_id,
                            'candidate_surname' =>  strtoupper($request->candidate_surname),
                            'candidate_other_name' => strtoupper($request->candidate_other_name),
                            'date_of_birth' => date("Y-m-d", strtotime($request->date_of_birth)),
                            'gender' => strtoupper($request->gender),
                        ]);
                    }
                }
                // Update Candidate
                Candidate::updateOrCreate(
                    ['candidate_no' => $request->candidate_no],
                    ["national_id" => $request->national_id]
                );
                //Create Candidate
                CenterCandidate::create([
                    "national_id" => $request->national_id,
                    "candidate_no" => $request->candidate_no,
                    "session" =>   $request->session,
                    "email" => $request->email,
                    "phone_number" => $request->phone_number,
                    "sponser" => "P",
                    "level" =>  $request->level,
                    'financial_year' => $financial_year,
                    "type" => 3,
                    "center_no" =>  $request->center,
                    "subject_number" =>  $request->number_of_subjects,
                ]);
                // Subjects
                foreach ($request->subject as $subject) {
                    $code = explode(',', $subject);
                    $subject_code = $code[0];
                    $subject_option = $code[1];
                    SubjectCandidate::create(
                        [
                            "national_id" => $request->national_id,
                            "candidate_no" => $request->candidate_no,
                            "subject_code" => $subject_code,
                            "subject_option" => $subject_option,
                            'level' => $request->level,
                            'session' => $request->session,
                            'financial_year' => $financial_year
                        ]
                    );
                }
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
                $specilalNeed = SpecialNeed::find($request->special_need);
                // CandidateArrangement

                CandidateArrangement::updateOrCreate(
                    ['candidate_no' => $candidate_no],
                    [
                        "candidate_no" => $candidate_no,
                        'arrangement_id' => $specilalNeed->arrangement_id,
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
                //Gurdian
                Guardian::updateOrCreate(
                    ['candidate' => $candidate_no, 'national_id' => $request->guardian_national_id],
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

                $candidate = CenterCandidate::where('national_id', '=', $request->national_id)
                    ->where('candidate_no', '=', $candidate_no)
                    ->where('session', '=', $request->session)
                    ->where('level', '=', $request->level)
                    ->where('financial_year', '=', $financial_year)
                    ->first();
                return  $candidate;
            } else if ($request->has("appending-subjects")) {
                // Update Candidate
                $candidate = CenterCandidate::where('candidate_no', '=', $candidate_no)
                    ->where('financial_year', '=', $financial_year)
                    ->where('level', '=', $request->level)
                    ->where('session', '=', $request->session)->first();
                $subject_number = ((int)($candidate->subject_number)) + ((int) $request->number_of_subjects);
                CenterCandidate::updateOrCreate(['candidate_no' => $request->candidate_no, "national_id" => $request->national_id], [
                    "subject_number" => $subject_number,
                ]);
                // Subjects
                foreach ($request->subject as $subject) {
                    $code = explode(',', $subject);
                    $subject_code = $code[0];
                    $subject_option = $code[1];
                    SubjectCandidate::create(
                        [
                            "national_id" => $request->national_id,
                            "candidate_no" => $request->candidate_no,
                            "subject_code" => $subject_code,
                            "subject_option" => $subject_option,
                            'level' => $request->level,
                            'session' => $request->session,
                            'financial_year' => $financial_year
                        ]
                    );
                }
                $candidate = CenterCandidate::where('national_id', '=', $request->national_id)
                    ->where('candidate_no', '=', $candidate_no)
                    ->where('session', '=', $request->session)
                    ->where('level', '=', $request->level)
                    ->where('financial_year', '=', $financial_year)
                    ->first();
                return   $candidate;
            } else if ($request->has("register_sessions")) {
                $candidate = CenterCandidate::where('candidate_no', '=', $candidate_no)
                    ->where('financial_year', '=', $financial_year)
                    ->where('level', '=', $request->level)->first();
                CenterCandidate::create([
                    "national_id" =>  $candidate->national_id,
                    "candidate_no" => $request->candidate_no,
                    "session" =>   $request->session,
                    "email" => $candidate->email,
                    "phone_number" => $candidate->phone_number,
                    "sponser" => "P",
                    "level" =>  $request->level,
                    'financial_year' => $financial_year,
                    "type" => 3,
                    "center_no" =>  $request->center,
                    "subject_number" =>  $request->number_of_subjects,
                ]);
                // Subjects
                foreach ($request->subject as $subject) {
                    $code = explode(',', $subject);
                    $subject_code = $code[0];
                    $subject_option = $code[1];
                    SubjectCandidate::create(
                        [
                            "national_id" =>  $candidate->national_id,
                            "candidate_no" => $request->candidate_no,
                            "subject_code" => $subject_code,
                            "subject_option" => $subject_option,
                            'level' => $request->level,
                            'session' => $request->session,
                            'financial_year' => $financial_year
                        ]
                    );
                }

                $candidate = CenterCandidate::where('national_id', '=',  $candidate->national_id)
                    ->where('candidate_no', '=', $candidate_no)
                    ->where('session', '=', $request->session)
                    ->where('level', '=', $request->level)
                    ->where('financial_year', '=', $financial_year)
                    ->first();
                return  $candidate;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function print(Request $request)
    {

        //  ob_start();
        // $decodedImg = grCodeGenerator($request->candidate_no,$request->candidate_no);
        // $pic = 'data://text/plain;base64,' .  $decodedImg;
        // //  Check if image was properly decoded
        // //  Open new PDF document and print image
        // $pdf = new  exFPDF();
        // $pdf->AddPage();
        // $pdf->Image($pic, 50, 50, 40, 40, 'png');
        // $pdf->Output('D', "Timetable" . $request->candidate_no . ".pdf");
        // ob_end_flush();
        // exit('0');

        if ($request->has("download")) {
            ob_start();
            $center = $request->centre_no;
            $candidateNo = $request->candidate_no;
            $session = $request->session;
            $level = $request->level;
            $candidate = DB::table('candidate_subject')
                ->select(
                    [
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
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                ->join('center_candidate', function ($join) {
                    $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                    $join->on('candidate_subject.level', '=', 'center_candidate.level');
                    $join->on('candidate_subject.session', '=', 'center_candidate.session');
                    $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                })
                ->join('fee_candidate_histories', function ($join) {
                    $join->on('center_candidate.id', '=', 'fee_candidate_histories.candidate_id');
                })

                ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
                ->where('fee_candidate_histories.status', '=', 1)
                ->where('center_candidate.financial_year', '=', date('Y') . '-' . (date('Y') + 1))
                ->where('center_candidate.level', '=', $level)
                ->where('center_candidate.session', '=', $session)
                ->where('center_candidate.candidate_no', '=', $candidateNo)->first();
            if (!is_null($candidate)) {
                $resultCentre = Center::where('center_no', '=', $center)->first();


                $file = fopen("/home/ecol/ecol.coltech.co.za/Instructions/Instructions.txt", "r");
                $pdf = new exFPDF();
                $pdf->SetMargins(8, 42, 8);
                $pdf->AliasNbPages();

                $pdf->AddPage();
                $column_width = ($pdf->GetPageWidth() - 30);
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->MultiCell($column_width, 6, "Lesotho General Certificate of Secondary Education (LGCSE)", 0, "C");
                $pdf->Ln(2);
                $pdf->MultiCell($column_width, 6, "Instructions to candidates:");
                $pdf->SetFont('helvetica', '', 13);
                while (!feof($file)) {
                    $line = fgets($file);
                    $pdf->MultiCellBlt($column_width, 6, chr(149), $line);
                    $pdf->Ln(4);
                }
                fclose($file);
                $pdf->Ln(10);
                $pdf->SetFont('helvetica', '', 11);
                $table = new easyTable($pdf, '{60,60,80, 60,80}', 'align:L{LLCC};border:0; border-color:#a1a1a1; ');

                $isPrintHeader = false;
                $pdf->AddPage();
                // QR code
                $candidate_josn = json_decode(json_encode($candidate), true);

                $decodedImg = grCodeGenerator($candidateNo, $candidate_josn);
                $pic = 'data://text/plain;base64,' .  $decodedImg;
                $pdf->Image($pic, 175, 5, 28, 25, 'png');
                $table->rowStyle('valign:M;border:0;paddingY:1;' . "#ccf2ff");
                $table->easyCell("Name of Centre", 'align:C;');
                $table->easyCell($resultCentre->center_no . ': ' . $resultCentre->center_name . '  (' . $resultCentre->district . ')', 'colspan:2; align:C;');
                $table->easyCell(date('d-m-Y '), ' align:C;');
                $table->printRow(4);
                $table->rowStyle('align:{CCCCC};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times; font-style:B;');
                $table->easyCell('Candidate No ');
                $table->easyCell('Candidate Names', 'colspan:2; align:C;');
                $table->easyCell('Sex');
                $table->easyCell('DOB');
                $table->printRow();


                $table->rowStyle('valign:M;border:0;paddingY:1;' . "#ccf2ff");
                $table->easyCell($candidate->candidate_no, 'align:C;');
                $table->easyCell($candidate->candidate_other_name . ' ' . $candidate->candidate_surname, 'colspan:2; align:C;');
                $table->easyCell($candidate->gender);
                $table->easyCell(date('d-m-Y ', strtotime($candidate->date_of_birth)), ' align:C;');
                $table->printRow(4);


                $subjects = explode(",", $candidate->subjects);

                foreach ($subjects as $subject) {

                    $code = explode(" ", $subject);
                    $results_timetable =  DB::table('timetable')
                        ->where('level', '=',    $level)
                        ->where('session', '=',   $session)
                        ->where('subject_code', '=',   $code[0]);
                    // MATHEMATICS core
                    if ($code[1] == "A" &&  $code[0] == "0178") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [1, 3]);

                        // MATHEMATICS Extended
                    } elseif ($code[1] == "B" &&  $code[0] == "0178") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [2, 4]);

                        //PHYSICAL SCIENCE core
                    } elseif ($code[1] == "A" &&  $code[0] == "0181") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [1, 2, 4]);


                        // PHYSICAL SCIENCE Extented
                    } elseif ($code[1] == "B" &&  $code[0] == "0181") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [1, 3, 4]);
                    } else {
                    }

                    $results_timetable = $results_timetable->get();
                    foreach ($results_timetable as $result_timetable) {

                        if (!$isPrintHeader) {
                            $table->rowStyle('font-style:B; border:B;border-color:#a1a1a1;');
                            $table->easyCell('Subject');
                            $table->easyCell($result_timetable->subject_code, ' align:L; ');
                            $table->easyCell($result_timetable->subject_name, 'colspan:3; align:L; ');
                            $table->printRow();
                            $isPrintHeader = true;
                        }


                        $table->rowStyle('border:B;border-color:#a1a1a1;');
                        $table->easyCell($result_timetable->paper_no, 'colspan:2; paddingX:20;');
                        $table->easyCell($result_timetable->pape_desc, 'colspan:2 align:L;');
                        $table->easyCell(date('l  F  d Y H:i', strtotime($result_timetable->date_time)) . " - " . date('H:i', strtotime($result_timetable->endTime)), 'colspan:3; align:C;');
                        $table->printRow();
                    }
                    $isPrintHeader = false;
                }

                if ($request->send) {
                    $cadidate = CenterCandidate::where('candidate_no', '=', $candidateNo)->first();
                    $pdfdoc = $pdf->Output('S', "Timetable" . $candidateNo  . ".pdf");
                    try {

                        $body = "Hi Please find the attached file
                                    <br><br>
                                    <br><br>
                                    Thank you
                                    <br>
                                    Examinations Council of Lesotho
                                    <br><br>For further assistance concerning registration, please contact us:<br>
                                    support@ecol.org.ls or examscouncil@ecol.org.ls";

                        Mail::send([], [], function ($message) use ($cadidate,  $body,  $pdfdoc) {
                            $message->to($cadidate->email)
                                ->subject('Timetable for ' .  $cadidate->candidate_no)
                                ->html($body)
                                ->attachData(
                                    $pdfdoc,
                                    "Timetable_$cadidate->candidate_no.pdf",
                                    ['mime' => 'application/pdf']
                                );
                        });
                    } catch (Exception $e) {
                        return response()->json(['success' => 1]);
                    }
                }
                $pdf->Output('D', "Timetable" . $candidateNo  . ".pdf");
                ob_end_flush();
                exit;
            } else {
                return  redirect()->back();
            }
        } else {
            return  redirect()->back();
        }
    }
    public function getEcoCashResponse(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'mobile_number' => 'required|numeric|digits:8',
        //     'total_amount' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()]);
        // }

        $uniqieID = "EcoCash" . time();
        $cURL = curl_init();
        $setopt_array = array(
            CURLOPT_URL => "http://10.1.11.53:444/openapi/PayMerchant",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode(
                array(
                    "msisdn" => "62228824",
                    "merchantNumber" => "62093622",
                    "merchCode" => "35431",
                    "amount" => "10",
                    "requestId" => "ZIBjS8vmemRAyJdx2uKn5ugeE",
                    "vendor_code" => "ecol",
                    "api_key" => "",
                    "checksum" => "",
                    "callbackurl" => ""
                )
            ),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                'Connection: keep-alive',
                'User-Agent: PHP-SOAP-CURL',
                'Content-Type: application/json; charset=utf-8',
                'Accept: application/json'
            )
        );
        curl_setopt_array($cURL, $setopt_array);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($cURL, CURLOPT_SSL_CIPHER_LIST, 'ECDHE-RSA-AES256-GCM-SHA384');
        $response = curl_exec($cURL);
        $err = curl_error($cURL);
        curl_close($cURL);
        if ($err) {
            dd($err);
            return response()->json(['error' => $err]);
        } else {
            dd($response);
            return response()->json(['success' => $response]);
        }
    }

    public function ecoCashCallBackUrl(Request $request)
    {
        // store call back data
        $callback = file_get_contents('php://input');
        $callbackurl = json_decode($callback, true);
        return $callbackurl;
    }

    //Cryptophraohy extension -> OpenSSL -> base64
    public function ecoCashChecksum($uniqieID)
    {
        $data = "ECOL" . "30626c006b435422a78445b524e6f436c231739d6b31d8d3bf1a10d47c63f9c3" . "000006" . $uniqieID;
        // 3coCa2ho83C01s02i
        // fetch private key from file and ready it
        $fp = fopen(public_path('cetificate/cert.key'), "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $binary_signature = "";
        openssl_sign($data, $binary_signature, $priv_key);
        $binary_signature = base64_encode($binary_signature);
        return   $binary_signature;
        // ==================== END Of our code ===================//
    }


    public function multiformPersonal(Request $request)
    {

        $currentPage = $request->current_section;
        $validationRules = [
            1 => [
                'alternative' => ['required'],
                'candidate_no' => [Rule::requiredIf(function () use ($request) {
                    return $request->alternative == "existing-candidate";
                }), 'exists:candidates,candidate_no'],
                'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                'candidate_other_name' => ['required'],
                'date_of_birth' => ['required'],
                'gender' => 'required',
                'national_id' => 'required',
                'candidate_other_name' => 'required',
                'date_of_birth' => 'required',
                'gender' => 'required',
                'candidate_surname' => ["required"],
            ],
            2 => [
                'email' => ['required', 'email'],
                'phone_number' => ['required', 'digits:8']
            ],

            3 => [
                'candidate_postal_address' => ['required'],
                'candidate_physical_address' => ['required'],
                'candidate_village' => ['required'],
                'candidate_district' => ['required']
            ],
            4 => [
                'special_need' => ['required'],
            ]

            // candidate_postal_address
        ];
        $validationMassages = [
            1 => [],
            2 => [],
            3 => [],
            4 => [],
        ];


        if ($request->has('candidate_no')) {
            $validationRules[1]['candidate_surname'][] =  "exists:candidates,candidate_surname,candidate_no, $request->candidate_no";
        }
        $validator = Validator::make($request->all(), $validationRules[$currentPage],  $validationMassages[$currentPage]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        switch ($currentPage) {
            case '1':
                $registered = false;
                $status = 0;
                $html = "";
                $bankconfirmation =   DB::table('candidate_confirmation');
                $bankconfirmation  = ($request->has('candidate_no') ?  $bankconfirmation->where('candidate_no', '=', $request->candidate_no) : $bankconfirmation);
                $bankconfirmation  = ($request->has('candidate_no') ?  $bankconfirmation : $bankconfirmation->where('national_id', '=', $request->national_id));
                $bankconfirmation =   $bankconfirmation->whereIn('checked_status', [0, 1])->count();

                $candidate_no  = ($request->has('candidate_no') ?  $request->candidate_no : null);
                $national_id  = ($request->has('national_id') ?  $request->national_id : null);
                // check if registered
                if ($this->is_partially_registered($request->alternative, $candidate_no, $national_id)  &&  $bankconfirmation == 0) {
                    if ($this->is_fully_registered($request->alternative, $candidate_no, $national_id)) {
                        $candidate = $this->is_fully_registered($request->alternative, $candidate_no, $national_id);
                        $session_count =   $candidate->count();
                        $candidateInfo = $candidate->first();
                        // $subject =$candidateInfo->
                        $html  .= "<label>Session</label>
                                    <select class='form-control register_sessions' aria-label='form-select-lg' name='register_sessions'>
                                   <option  value=''>Select session</option>
                                  ";
                        $html  .= ($session_count >= 2)  ? " " : "<option  value='new-session'>New Session</option>";

                        foreach ($candidate as $attribute) {
                            $html .= "<option value='$attribute->session'>$attribute->session </option>";
                        }
                        $session_year = explode('-', $candidateInfo->financial_year)[0];
                        $html .= "</select>";
                        $html .= "<div class='card border-success text-center mt-2' >
                                <h4 class='card-header text-white bg-success py-2'>$candidateInfo->candidate_surname  $candidateInfo->candidate_other_name $candidateInfo->date_of_birth</h4>
                            <div class='card-body'>
                                 <input type='hidden' id='national_id' name='national_id' value='$candidateInfo->national_id' />
                                 <input type='hidden' id='candidate_no' name='candidate_no' value='$candidateInfo->candidate_no' />
                                 <input type='hidden' id='candidate_surname' name='candidate_surname' value='$candidateInfo->candidate_surname' />
                                 <input type='hidden' id='candidate_other_name' name='candidate_other_name' value='$candidateInfo->candidate_other_name' />
                                 <input type='hidden' id='gender' name='gender' value='$candidateInfo->gender' />
                                 <input type='hidden' id='email' name='email' value='$candidateInfo->email' />
                                <h5 class='card-title display-2' id='session-title'>Registered : $candidateInfo->session  $session_year </h5>
                                <row class='row registed_subject'>

                               </row>
                            </div>
                            </div>";
                        $registered = true;
                        $status = 2;
                        return response()->json(['status' => $status, 'output' => $html, 'registered' => $registered]);
                    } else {
                        $candidate = $this->is_partially_registered($request->alternative, $candidate_no, $national_id);
                        $candidaterouteLogin = route('candidate.login');
                        $homeprivaterote = route('private.cadidate');
                        $candidateInfo = $candidate->first();
                        $html = "<div class='card border-success text-center mt-2' >
                                        <h4 class='card-header text-white bg-success py-2'>$candidateInfo->candidate_surname  $candidateInfo->candidate_other_name $candidateInfo->date_of_birth</h4>
                                    <div class='card-body'>
                                        <h5 class='card-title display-2'>Registered at <b>$candidateInfo->center_no</b></h5>
                                        <a class='link-primary text-left' target='_blank' href='$homeprivaterote'>Home</a>
                                         <a class='btn btn-primary' target='_blank' href='$candidaterouteLogin'>Login</a>
                                    </div>
                                </div>";
                        $status = 1;
                        $registered = true;
                        return response()->json(['status' => $status, 'output' => $html, 'registered' => $registered]);
                    }
                } else {
                    return true;
                }

                break;
            case '2':
                return true;
                break;
            case '3':
                return true;
                break;
            case '4':
                return true;
                break;

            default:
                # code...
                break;
        }
    }


    private function sendConfirmation($national_id, $candidate_no, $session, $financial_year)
    {

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
                    'fee_candidate_histories.amount',

                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('fee_candidate_histories', function ($join) {
                $join->on('center_candidate.id', '=', 'fee_candidate_histories.candidate_id');
            })
            ->where("center_candidate.candidate_no", '=', $candidate_no)
            ->where("center_candidate.national_id", '=', $national_id)
            ->where("center_candidate.session", '=', $session)
            ->where("center_candidate.financial_year", '=', $financial_year)
            ->first();

        $email = $candidate->email;
        $amount = $candidate->amount;

        // $candidate=
        $emails = Setting::whereIn('meta_field', [
            //  'business_email',
            'finance_email',

        ])->pluck('meta_value')->toArray();
        Mail::to($email)
            ->cc($emails)
            ->send(new CandidateInvoiceMail($candidate, $amount));
    }

    private function getFilesNumber($candidate_no)
    {
        $candidate_no = str_pad($candidate_no, 9, '0', STR_PAD_LEFT);
        $files = Storage::files("public/bankStatement/$candidate_no");
        return  count($files);
    }
}
