<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Amendment;
use App\Models\Candidate;
use App\Models\CandidateUser;
use App\Models\Center;
use Illuminate\Http\Request;

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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SplFileObject;
use Yajra\DataTables\Facades\DataTables;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $center = Center::with('subjects')->where('center_no', '=', auth()->user()->center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $levels = Level::where('level', '=', $center->level)->get();
        $sessions = Session::where('is_active', '=', 1)->whereIn('session', $centerSessions)->get();
        $subjects =  $center->subjects;
        $specialNeeds = SpecialNeed::get();
        $guardian_types =  GuardianType::get();
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        return view('school.amendments', compact('center', 'levels', 'subjects', 'sessions', 'districts', 'specialNeeds', 'guardian_types'));
    }


    public function searchCandidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_no' => 'required|numeric|exists:candidates,candidate_no',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
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
            ->where("center_candidate.financial_year", "=", date('Y') . '-' . (date('Y') + 1))
            ->get();
        $candidate =  Candidate::findOrFail($request->candidate_no);
        if (count($candidate_registration) < 1) {
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
                        <div class='form-group col-md-6'>
                                <label for='type'>Type</label>
                                <select name='type' class='form-control' id='type'>
                                <option value=''>Select Type</option>
                                <option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                </select>
                            <span class='help-block'></span>
                        </div>
                        <div class='form-group col-md-6'>
                            <label for='sponsor'>Sponsor</label>
                            <select name='sponsor' class='form-control' id='sponsor'>
                            <option value=''>Select Sponsor</option>
                            <option value='O'>O</option>
                            <option value='M'>M</option>
                            <option value='N'>N</option>
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
                        <span class='help-block' id='subjects-errors'></span>
                    </div>";
            $status = 1;
        } elseif (count($candidate_registration) < 2) {
            $candidate_registration = $candidate_registration->first();
            if ($candidate_registration->session != "November") {
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
                                <div class='form-group col-md-6'>
                                        <label for='type'>Type</label>
                                        <select name='type' class='form-control' id='type'>
                                        <option value=''>Select Type</option>
                                        <option value='1'>1</option>
                                        <option value='2'>2</option>
                                        <option value='3'>3</option>
                                        </select>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-6'>
                                    <label for='sponsor'>Sponsor</label>
                                    <select name='sponsor' class='form-control' id='sponsor'>
                                    <option value=''>Select Sponsor</option>
                                    <option value='O'>O</option>
                                    <option value='M'>M</option>
                                    <option value='N'>N</option>
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
            } else {
                return response()->json(['errors' => ['candidate_no' => ['candidate already registerd']]]);
            }
        } elseif (count($candidate_registration) == 2) {
            return response()->json(['errors' => ['candidate_no' => ['candidate already registerd']]]);
        }
        return response()->json(['status' =>  $status, 'html' =>  $output]);
    }

    public function store(Request $request)
    {
        $validation_rules = [
            'candidate_no' => ['required'],
            'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
            'candidate_surname' => ['required'],
            'candidate_other_name' => ['required'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:-8 years'],
            'gender' => ['required', 'in:M,F'],
            'sponsor' => ['required', 'in:M,O,K'],
            'type' => ['required', 'in:1,2,3'],
            'level' => ['required'],
            'type' => ['required'],
            'session' => ['required'],
            'subject' => ['required'],
        ];
        $validation_messages = [];
        $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        switch ($request->level) {
            case 'G7ELT':
                $validation_rules = [
                    'guardian_national_id' => ['required'],
                    'guardian_surname' => ['required'],
                    'guardian_name' => ['required'],
                    'guardian_phone_number' => ['required', 'numeric'],
                ];
                $validation_messages = [];
                $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                $candidate_no = $request->candidate_no;
                $center_no = auth()->user()->center_no;
                $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
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
                $validation_rules['subjects'][] = new SubjectsGrouping($center_no);
                $validation_rules['subjects'][] = new CheckDupsSubject();
                $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                $candidateUserArray = array();
                if ($request->candidate_no == "*") {
                    $new_candidate = Candidate::whereDate('date_of_birth', date("Y-m-d", strtotime($request->date_of_birth)))
                        ->where('candidate_other_name', '=', strtoupper($request->candidate_other_name))
                        ->where('gender', '=', $request->gender)
                        ->where('candidate_surname', '=', strtoupper($request->candidate_surname))->first();
                    $candidate_no = (!$new_candidate) ? getNextCandidateNumber() : $new_candidate->candidate_no;
                    $request->merge(['candidate_no' => $candidate_no]);
                    //National id
                    $validation_rules['national_id'][] = Rule::unique('center_candidate')
                        ->where(function ($query) use ($request) {
                            return $query->where('national_id', $request->national_id)
                                ->where('level', '=', $request->level)
                                ->where('session', '=', 'November')
                                ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                        });
                    //Candidate Number
                    if ($new_candidate) {
                        $validation_rules['candidate_no'][] = Rule::unique('center_candidate')
                            ->where(function ($query) use ($request) {
                                return $query->where('candidate_no', $request->candidate_no)
                                    ->where('level', '=', $request->level)
                                    ->where('session', '=', 'November')
                                    ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                            });
                    }

                    $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()]);
                    } else {
                        // Assign Candidate Numberber
                        if (!$new_candidate) {
                            Candidate::create([
                                'candidate_no' => $request->candidate_no,
                                'national_id' => $request->national_id,
                                'candidate_surname' =>  strtoupper($request->candidate_surname),
                                'candidate_other_name' => strtoupper($request->candidate_other_name),
                                'date_of_birth' => date("Y-m-d", strtotime($request->date_of_birth)),
                                'gender' => $request->gender,
                            ]);
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
                        return response()->json(['errors' => $validator->errors()]);
                    } else {
                        $this->register($request);
                    }
                }

                $guardian_national_id = $request->guardian_national_id;
                $guardian_surname  = strtoupper($request->guardian_surname);
                $guardian_name  = strtoupper($request->guardian_name);
                $guardian_phone_number   = $request->guardian_phone_number;
                $guardian_email   = "$center_no@ecol.org.ls";
                $guardian_village   = strtoupper($request->guardian_village);

                //Candidate Profile
                DB::table('candidates')
                    ->where('candidate_no', $request->candidate_no)
                    ->limit(1)  // optional - to ensure only one record is updated.
                    ->update([
                        'national_id' => $request->national_id
                    ]);


                // Candidate Arrangements
                $arrangementArray[] = [
                    'candidate_no' => $candidate_no,
                    'arrangement_id' => 1
                ];

                //Address Candidate
                $addressArray[] = [
                    "postal_address" => $guardian_village,
                    "physical_address" => $guardian_village,
                    "village" => $guardian_village,
                    "user_id" => $request->national_id,
                    "user_type" => Candidate::class,
                    "district_code" => $center->district_code,
                    "district" => $center->district,
                ];
                // Guardian Infomation
                $guardian = DB::table('guardians')->select(
                    [
                        'national_id'
                    ]
                )->where('candidate', '=', $candidate_no)
                    ->where('phone_number', '=', $guardian_phone_number)->first();

                $guardianArray[] = [
                    "candidate" => $candidate_no,
                    'national_id' => isset($guardian) ? $guardian->national_id : $guardian_national_id,
                    "guardian_type" => 1,
                    "name" => $guardian_name,
                    "surname" => $guardian_surname,
                    "email" => $guardian_email,
                    "phone_number" => $guardian_phone_number
                ];
                // Guardian addresss
                $addressArray[] =
                    [
                        "postal_address" => $guardian_village,
                        "physical_address" => $guardian_village,
                        "village" => $guardian_village,
                        "user_id" =>  isset($guardian) ? $guardian->national_id : $guardian_national_id,
                        "user_type" => Guardian::class,
                        "district_code" => $center->district_code,
                        "district" => $center->district,
                    ];

                //Addresss
                DB::table('addresses')->upsert(
                    $addressArray,
                    [
                        'user_id',
                        'user_type'
                    ]
                );
                //candidate arrangement
                DB::table('candidate_arrangement')->upsert(
                    $arrangementArray,
                    [
                        'candidate_no',
                        'arrangement_id'
                    ]
                );
                //Guardians
                DB::table('guardians')->upsert(
                    $guardianArray,
                    [
                        'candidate',
                        'national_id'
                    ]
                );
                break;
            case 'LGCSE':
                $candidate_no = $request->candidate_no;
                $center = auth()->user()->center_no;
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
                $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                $candidateUserArray = array();
                if ($request->candidate_no == "*") {
                    $new_candidate = Candidate::whereDate('date_of_birth', date("Y-m-d", strtotime($request->date_of_birth)))
                        ->where('candidate_other_name', '=', $request->candidate_other_name)
                        ->where('gender', '=', $request->gender)
                        ->where('candidate_surname', '=', $request->candidate_surname)->first();
                    $candidate_no = (!$new_candidate) ? getNextCandidateNumber() : $new_candidate->candidate_no;
                    $request->merge(['candidate_no' => $candidate_no]);


                    //National id
                    $validation_rules['national_id'][] = Rule::unique('center_candidate')
                        ->where(function ($query) use ($request) {
                            return $query->where('national_id', $request->national_id)
                                ->where('level', '=', $request->level)
                                ->where('session', '=', 'November')
                                ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                        });

                    //Candidate Number
                    if ($new_candidate) {
                        $validation_rules['candidate_no'][] = Rule::unique('center_candidate')
                            ->where(function ($query) use ($request) {
                                return $query->where('candidate_no', $request->candidate_no)
                                    ->where('level', '=', $request->level)
                                    ->where('session', '=', 'November')
                                    ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                            });
                    }
                    $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()]);
                    } else {

                        // Assign Candidate Number
                        if (!$new_candidate) {
                            Candidate::create([
                                'candidate_no' => $request->candidate_no,
                                'national_id' => $request->national_id,
                                'candidate_surname' => strtoupper($request->candidate_surname),
                                'candidate_other_name' => strtoupper($request->candidate_other_name),
                                'date_of_birth' => date("Y-m-d", strtotime($request->date_of_birth)),
                                'gender' => $request->gender,
                            ]);
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
                        return response()->json(['errors' => $validator->errors()]);
                    } else {
                        $this->register($request);
                    }
                }

                DB::table('candidates')
                    ->where('candidate_no', $request->candidate_no)
                    ->limit(1)  // optional - to ensure only one record is updated.
                    ->update([
                        'national_id' => $request->national_id
                    ]);

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
                break;
            default:
                break;
        }
        return response()->json(['success' => "You have successfully added candidate"]);
    }

    private function register($request)
    {
        $subject_number = count($request->subjects);
        $center = auth()->user()->center_no;
        $candidate = new CenterCandidate();
        $candidate->candidate_no = $request->candidate_no;
        $candidate->national_id = $request->national_id;
        $candidate->center_no =  $center;
        $candidate->subject_number = $subject_number;
        $candidate->type = $request->type;
        $candidate->session = $request->session;
        $candidate->sponser = $request->sponsor;
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
    public function editCandidate($id)
    {

        $center_no = auth()->user()->center_no;
        $candidate = $this->is_registered($id);
        $prifix = substr(date('Y'), 2, 4);
        $editable = substr($candidate->candidate_no, 0, 2) == $prifix ? true : false;
        $url = route('center.candidates.update', $id);
        $level = Level::where('level', '=', $candidate->level)
            ->where('is_active', '=', 1)->first()->id;
        $session = Session::where('session', '=', $candidate->session)
            ->where('is_active', '=', 1)
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
        return response()->json(['candidate' => $candidate, 'action' => $url, $subjectsHTML, 'editable' => $editable, 'editable_fields' => $editable_fields]);
    }
    public function editCandidateDOB($id)
    {
        $center_no = auth()->user()->center_no;
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
            ->where('center_candidate.center_no', '=', $center_no)
            ->first();


        $checkAmendCandidate = Amendment::where('candidate_no', '=', $candidate->candidate_no)->first();
        $url = route('center.candidates.updateCandidateDOB',  $candidate->candidate_no);
        if ($checkAmendCandidate) {
            return response()->json(['candidate' =>  $checkAmendCandidate, 'action' => $url]);
        }
        return response()->json(['candidate' => $candidate, 'action' => $url]);
    }
    public  function deleteCandidate($id)
    {
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
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
        SubjectCandidate::where('candidate_no', '=', $candidate->candidate_no)
            ->where('session', '=', $candidate->session)
            ->where('level', '=', $candidate->level)
            ->where('financial_year', '=', $candidate->financial_year)
            ->delete();
        CenterCandidate::where('center_candidate.id', '=', $id)->delete();
        CandidateUser::where('candidate_no', '=', $candidate->candidate_no)
            ->where('session', '=', $candidate->session)
            ->where('financial_year', '=', $candidate->financial_year)->delete();
        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);
    }

    public  function deleteCandidates(Request $request)
    {
        foreach ($request->candidateNumbers as  $id) {
            $candidate = DB::table('candidate_subject')
                ->select(
                    [
                        'center_candidate.id',
                        'center_candidate.center_no',
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
            SubjectCandidate::where('candidate_no', '=', $candidate->candidate_no)
                ->where('session', '=', $candidate->session)
                ->where('level', '=', $candidate->level)
                ->where('financial_year', '=', $candidate->financial_year)
                ->delete();
            CenterCandidate::where('center_candidate.id', '=', $id)->delete();
            CandidateUser::where('candidate_no', '=', $candidate->candidate_no)
                ->where('session', '=', $candidate->session)
                ->where('financial_year', '=', $candidate->financial_year)->delete();
        }
        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);
    }

    public function updateCandidate(Request $request, $id)
    {
        // 'numeric',
        //'regex:/^(\d{8}|\d{13})$/',
        $validation_rules = [
            'candidate_no' => ['required'],
            'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
            'candidate_surname' => ['required'],
            'candidate_other_name' => ['required'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:-10 years'],
            'gender' => ['required', 'in:M,F'],
            'sponser' => ['required', 'in:M,O,K'],
            'type' => ['required', 'in:1,2,3'],
            'level' => ['required'],
            'type' => ['required'],
            'session' => ['required'],
            'subject' => ['required'],
        ];
        $validation_messages = [];
        $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidate_no = $request->candidate_no;
        $center = auth()->user()->center_no;
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
        $validator = Validator::make($request->all(), $validation_rules, $validation_messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
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
        CandidateUser::where('candidate_no', '=', $candidate_no)
            ->update([
                'national_id' => $request->national_id,
                'username' => $request->candidate_surname . " " . $request->candidate_other_name,
                'password' =>  Hash::make(str_replace('-', '', date("Y-m-d", strtotime($request->date_of_birth)))),
                'candidate_password' => str_replace('-', '', date("Y-m-d", strtotime($request->date_of_birth))),
            ]);
        //Center Candidate
        $candidate = CenterCandidate::find($id);
        $candidate->subject_number = $subject_number;
        $candidate->type = $request->type;
        $candidate->sponser = $request->sponser;
        $candidate->national_id = $request->national_id;
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
            ->where('session', '=', 'November')
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
        return response()->json(['success' => 'Successfully updated the records']);
    }
    public function updateCandidateDOB(Request $request, $id)
    {
        Amendment::updateOrCreate([
            'candidate_no'   => $id,
        ], [
            'candidate_no'   => $id,
            'candidate_surname' => $request->candidate_surname,
            'candidate_other_name' => $request->candidate_other_name,
            'date_of_birth' => date("Y-m-d", strtotime($request->date_of_birth)),
            'gender' => $request->gender,
            'amend_date' => date("Y-m-d")

        ]);
        return response()->json(['status' => true, 'candidate' => $request->candidate_surname]);
    }


    public function showCandidate($id)
    {
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.national_id',
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
            ->where("center_candidate.id", '=', $id);
        $subjects = $candidate->get();
        $candidate_inforamtion = $candidate->first();

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
            'subjects' => $subjects,
            'guardian' => $guardian,
            'paid_fee' => $paid_fee
        ]);
    }

    public function fatchAmendments(Request $request)
    {
        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();


        $candidates = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.session',
                    'center_candidate.financial_year',
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
            ->where('center_candidate.financial_year', '=',  $financial_year)
            ->where('center_candidate.session', '=',  $session->session)
            ->whereIn('center_candidate.sponser', ['O', 'N', 'M', 'K']);
        if (!empty($request->level_filter)) {
            $candidates = $candidates->where('center_candidate.level', '=', $request->level_filter);
        }
        $candidates = $candidates->where('center_candidate.center_no', '=',  $center_no);
        return Datatables::of($candidates)
            ->editColumn('candidate_no', function ($model) {
                return str_pad($model->candidate_no, 9, '0', STR_PAD_LEFT);
            })
            ->editColumn('national_id', function ($model) {
                return str_pad($model->national_id, 12, '0', STR_PAD_LEFT);
            })
            ->editColumn('candidate_other_name', function ($model) {
                return Str::limit($model->candidate_other_name, 8);
            })
            ->editColumn('subjects', function ($model) {
                $output = "";
                $subjects = explode(",", $model->subjects);
                foreach ($subjects as $subject) {
                    $output .= ' <span class="subject-data">' . $subject . '</span>';
                }
                return $output;
            })
            ->addColumn('actions', function ($model) {
                $user = auth()->user();
                $actions = '<div class="btn-group">';
                if ($user->isAbleTo('amendments-update') &&   is_activate($model->level)) {
                    $actions .= is_paid($model->candidate_no, $model->national_id, $model->level, $model->session, $model->financial_year) ? "" :
                        '<a  class="edit-candidate updateCandidate"   data-action="' . route('center.candidates.edit', $model->id) . '"  type="button" rel="tooltip" title="Edit">
                                <i class="fas fa-edit"></i>
                                    </a>';
                }
                $actions .= '<a  class="show-candidate" data-action="' . route('center.candidates.showCandidate', $model->id) . '"  type="button"   rel="tooltip" title="Edit">
                                  <i class="fas fa-eye"></i>
                            </a>';



                if ($user->isAbleTo('amendments-delete')) {
                    $actions .= is_paid($model->candidate_no, $model->national_id, $model->level, $model->session, $model->financial_year) ? "" : '<a class="delete-candidate" data-id="' . $model->id . '" type="button" rel="tooltip" title="Delete">
                                    <i class="far fa-trash-alt"></i>
                            </a>';
                }
                $actions .= '</div>';
                return    $actions;
            })
            ->addColumn('checkbox', function ($model) {
                return "<input type='checkbox' class='candidates' name='candidates[]' value='$model->id'>";
            })
            ->rawColumns(['actions', 'checkbox', 'subjects', 'candidate_other_name'])
            ->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response
     */
    public function registered(Request  $request)
    {

        /******  Some Default Values Start   ******/
        $candidates_filter = $request->candidates_filter;
        $search = "";
        $candidates_sort = $request->candidates_sort;
        $output = "";
        $outputCandidateUser = "";
        $outputPrivate = "";


        /******  Some Default Values End   ******/

        /******  Check Main Search Bar Have Any Text- Start******/

        $search = $request->search_txt;

        /******  Check Main Search Bar Have Any Text- End******/

        // Set Up
        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        $level = $center->level;
        $candidates = DB::table('candidate_subject')
            ->select(
                'center_candidate.candidate_no',
                'center_candidate.id',
                'center_candidate.type',
                'center_candidate.national_id',
                'center_candidate.session',
                'center_candidate.financial_year',
                'center_candidate.level',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
            order by candidate_subject.subject_code separator ' ,') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->whereIn('center_candidate.sponser', ['O', 'N', 'M', 'K'])
            ->where('center_candidate.center_no', '=', $center_no)
            ->where('center_candidate.financial_year', '=', $financial_year)
            ->where('center_candidate.session', '=', $session->session)
            ->where('center_candidate.level', '=', $level);

        $candidate_user = DB::table('candidate_subject')
            ->select(
                'center_candidate.id',
                'center_candidate.national_id',
                'center_candidate.candidate_no',
                'center_candidate.session',
                'center_candidate.financial_year',
                'center_candidate.level',
                'candidate_users.candidate_password',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'center_candidate.email',
                'center_candidate.phone_number',
                'candidates.date_of_birth'
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('candidate_users', 'candidate_users.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.national_id', '=', 'center_candidate.national_id');
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->whereIn('center_candidate.sponser', ['O', 'N', 'M', 'K'])
            ->where('center_candidate.center_no', '=',   $center_no)
            ->where('center_candidate.level', '=', $level)
            ->where('center_candidate.session', '=', $session->session)
            ->where('center_candidate.financial_year', '=', $financial_year);

        $privateCandidates = DB::table('candidate_subject')
            ->select(
                'center_candidate.candidate_no',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
            order by candidate_subject.subject_code separator ' ,') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')

            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->whereNotIn('center_candidate.sponser', ['O', 'N', 'M', 'K'])
            ->where('center_candidate.center_no', '=',   $center_no)
            ->where('center_candidate.level', '=', $level)
            ->where('center_candidate.session', '=', $session->session)
            ->where('center_candidate.financial_year', '=', $financial_year);
        if (!is_null($search)) {
            if ($candidates_sort == 1) {

                $candidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('center_candidate.center_no', "ASC")
                    ->limit($candidates_filter);

                $candidate_user->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('center_candidate.center_no', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('center_candidate.center_no', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 2) {
                $candidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('candidate_surname', "ASC")
                    ->limit($candidates_filter);

                $candidate_user->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('candidate_surname', "ASC")
                    ->limit($candidates_filter);



                $privateCandidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('candidate_surname', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 3) {
                $candidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('candidate_other_name', "ASC")
                    ->limit($candidates_filter);

                $candidate_user->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('candidate_other_name', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('candidate_other_name', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 4) {
                $candidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('sponser', "ASC")
                    ->limit($candidates_filter);

                $candidate_user->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('sponser', "ASC")
                    ->limit($candidates_filter);

                $privateCandidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('sponser', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 5) {
                $candidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('type', "ASC")
                    ->limit($candidates_filter);

                $candidate_user->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('type', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->where('candidate_surname', 'LIKE', "%{$search}%")
                    ->orWhere('candidate_other_name', 'LIKE', "%{$search}%")
                    ->orderBy('type', "ASC")
                    ->limit($candidates_filter);
            }
        } else {

            if ($candidates_sort == 1) {
                $candidates->orderBy('center_candidate.center_no', "ASC")
                    ->limit($candidates_filter);

                $candidate_user->orderBy('center_candidate.center_no', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->orderBy('center_candidate.center_no', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 2) {
                $candidates->orderBy('candidate_surname', "ASC")
                    ->limit($candidates_filter);
                $candidate_user->orderBy('candidate_surname', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->orderBy('candidate_surname', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 3) {
                $candidates->orderBy('candidate_other_name', "ASC")
                    ->limit($candidates_filter);
                $candidate_user->orderBy('candidate_other_name', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->orderBy('candidate_other_name', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 4) {
                $candidates->orderBy('sponser', "ASC")
                    ->limit($candidates_filter);
                $candidate_user->orderBy('sponser', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->orderBy('sponser', "ASC")
                    ->limit($candidates_filter);
            } else if ($candidates_sort == 5) {
                $candidates->orderBy('type', "ASC")
                    ->limit($candidates_filter);
                $candidate_user->orderBy('type', "ASC")
                    ->limit($candidates_filter);
                $privateCandidates->orderBy('type', "ASC")
                    ->limit($candidates_filter);
            }
        }

        $candidates = $candidates->get();
        $candidate_user = $candidate_user->get();
        $privateCandidates =  $privateCandidates->get();


        if ($candidates->count()) {
            $output .= '<table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Candidate No</th>
                                <th>Surname</th>
                                <th>Other Name</th>
                                <th>Date of Birth</th>
                                <th>Type</th>
                                <th>Sponsor</th>
                                <th>No.</th>
                                <th colspan="8">Subject</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>';



            foreach ($candidates as $result) {
                $action = '<a  class="show-candidate" data-action="' . route('center.candidates.showCandidate', $result->id) . '"  type="button"   rel="tooltip" title="Edit">
                                  <i class="fas fa-eye"></i>
                            </a>';

                //fee-indicator
                $indicator_color = is_paid_sponsored($result->id)->color;
                $indicator = "<span class='fee-indicator' style='border-left: 6px solid $indicator_color'></span>";
                $output .= "<tr id='delete" . (int)$result->candidate_no . "'>
                           <td> $indicator" . str_pad($result->candidate_no, 9, '0', STR_PAD_LEFT) . "</td>
                           <td>  $result->candidate_surname </td>
                           <td> $result->candidate_other_name </td>
                           <td>  $result->date_of_birth </td>
                           <td>  $result->type </td>
                           <td> $result->sponser  </td>
                            <td>  $result->subject_number</td>
                          ";
                $subjects = explode(",", $result->subjects);
                foreach ($subjects as $subject) {
                    $output .= ' <td>' . $subject . '</td>';
                }
                $output .= "<td>$action</td></tr>";
            }
            $output .=  '</tbody>
                        </table>';
        } else {
            $output =  '<div>
                        No Candidates
                        </div>';
        }

        if ($candidate_user->count()) {
            $outputCandidateUser .= '<table class="table table-striped">
                        <thead>
                            <tr>
                              <th>National ID</th>
                               <th>Candidate No</th>
                                <th>Surname</th>
                                <th>Other name</th>
                                <th>Date of birth</th>
                                <th>Email</th>
                                <th>Phone number</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>';

            foreach ($candidate_user as $result) {
                //fee-indicator
                $indicator_color = is_paid_sponsored($result->id)->color;
                $indicator = "<span class='fee-indicator' style='border-left: 6px solid $indicator_color'></span>";

                $outputCandidateUser  .= "<tr>
                           <td> $indicator" . str_pad($result->national_id, 12, '0', STR_PAD_LEFT)  . "</td>
                           <td>" . str_pad($result->candidate_no, 9, '0', STR_PAD_LEFT)  . "</td>
                           <td> $result->candidate_surname </td>
                           <td> $result->candidate_other_name </td>
                           <td> $result->date_of_birth </td>
                           <td> $result->email</td>
                           <td> $result->phone_number </td>
                           <td> $result->candidate_password</td>
                           </tr>
                          ";
            }
            $outputCandidateUser  .=  '</tbody>
                        </table>';
        } else {
            $outputCandidateUser =  '<div>
                        No Candidates
                        </div>';
        }

        if ($privateCandidates->count()) {
            $outputPrivate .= '<table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Candidate No</th>
                                    <th>Surname</th>
                                    <th>Other Name</th>
                                    <th>Date of Birth</th>
                                    <th>Type</th>
                                    <th>Sponsor</th>
                                    <th colspan="8">Subject</th>
                                </tr>
                            </thead>
                            <tbody>';

            foreach ($privateCandidates  as $result) {

                $outputPrivate .=
                    "<tr>
                           <td> $result->candidate_no</td>
                           <td> $result->candidate_surname</td>
                           <td> $result->candidate_other_name</td>
                           <td>  $result->date_of_birth </td>
                           <td>  $result->type</td>
                           <td>  $result->sponser</td>
                          ";
                $subjects = explode(",", $result->subjects);
                foreach ($subjects as $subject) {
                    $outputPrivate .= ' <td>' . $subject . '</td>';
                }
            }
            $outputPrivate .=  '</tr>
                             </tbody>
                        </table>';
        } else {
            $outputPrivate =  '<div>
                        No Candidates
                        </div>';
        }

        return response()->json(['table' => $output, 'private_table' => $outputPrivate, 'candidate_user' => $outputCandidateUser]);
    }
    public function importCandidatate(Request $request)
    {
        set_time_limit(0);
        $validator = Validator::make($request->all(), [
            'level' => 'required',
            'session' => 'required',
            'fileup' => 'required|mimes:csv,txt',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }


        $auth_center_no = auth()->user()->center_no;
        $level = $request->level;
        $session = $request->session;
        // Subject Center
        $center = Center::with('subjects')->where('center_no', '=', $auth_center_no)->first();
        $subjects = $center->subjects()->get();
        $subject_codes = $subjects->pluck('subject_code')->toArray();
        // Change to String
        $subject_codes = implode(",", $subject_codes);
        $file = file($request->fileup->getRealPath());
        $delimiter = $this->getFileDelimiter($request->fileup->getRealPath());
        $errors = [];
        $candidates = $this->csvToArray($file, $delimiter);
        if (count($candidates) == 0) {
            $error = [
                'messages' =>  ['center_no' => ['invalid centre number']],
                'row' => 1,
            ];
            array_push($errors, $error);
        }

        $registerCandidate = 0;
        $totalCandidates = count($candidates);
        $rownumber = 0;
        try {
            //
            $centerCandidateArray = array();
            $candidateProfileArray = array();
            $candidateUserArray = array();
            $subjectCandidateArray = array();
            switch ($level) {
                case 'LGCSE':
                    for ($j = 0; $j < count($candidates); $j++) {
                        $rownumber = $j;
                        $failed = false;
                        $subject_code = 12;
                        $subject_option = 13;
                        $subjects = ['subjects' => array()];
                        $subject_number = $candidates[$j][11];
                        $keys = array_keys($candidates[$j]);
                        $last = end($keys);
                        $orginal_col_sponser = ($subject_number * 2) + 11 + 1;
                        $sponsor_col = $this->checkKeyExists($candidates[$j], $orginal_col_sponser) ?   $orginal_col_sponser :  $last; //($candidates[$j][10] * 2) + 10 + 1;
                        // Canidate is not find in Candidates Table Create New
                        $dateOfBirth = str_pad($candidates[$j][9], 8, '0', STR_PAD_LEFT);
                        $year   = substr($dateOfBirth, 4, 8);
                        $month  = substr($dateOfBirth, 2, 2);
                        $day = substr($dateOfBirth, 0, 2);
                        $center_no = $candidates[$j][0];
                        $national_id  = $candidates[$j][1];
                        $candidate_no  = $candidates[$j][2];
                        $candidate_surname  = strtoupper($candidates[$j][3]);
                        $candidate_other_name  = strtoupper($candidates[$j][4]);
                        $gender  = strtoupper($candidates[$j][7]);
                        $dateOfBirth = date("Y-m-d", mktime(0, 0, 0, $month,  $day, $year));
                        $sponser = $candidates[$j][$sponsor_col];
                        $type = $candidates[$j][6];
                        $data = [
                            'center_no' => $center_no,
                            'candidate_no' => $candidate_no,
                            'national_id' => $national_id,
                            'candidate_surname' =>  $candidate_surname,
                            'candidate_other_name' => $candidate_other_name,
                            'date_of_birth' => $dateOfBirth,
                            'gender' => $gender,
                            'type' => $type,
                            'sponser' => $sponser,
                            'subject_number' => $subject_number
                        ];
                        $validation_rules = [
                            'center_no' => ['required', 'in:' . $auth_center_no],
                            'candidate_no' => ['required'],
                            'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                            'candidate_surname' => ['required'],
                            'candidate_other_name' => ['required'],
                            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:-10 years'],
                            'gender' => ['required', 'in:M,F'],
                            'sponser' => ['required', 'in:M,O,K'],
                            'type' => ['required', 'in:1,2,3'],
                        ];

                        $validation_messages = [
                            // add custom error messages
                            'candidate_no.unique' => "The candidate is already registered",
                            'candidate_surname.exists' => "The candidate number is invalid",
                            'national_id.unique' => "The national ID is already registered",
                        ];
                        $validator = Validator::make($data, $validation_rules, $validation_messages);
                        if ($validator->fails()) {
                            $failed = true;
                            $error = [
                                'messages' =>  $validator->errors()->all(),
                                'row' => $j + 1,
                            ];
                            array_push($errors, $error);
                        } else {
                            // Check If require new candidate
                            if ($candidate_no == "*") {
                                $new_candidate = Candidate::whereDate('date_of_birth', $dateOfBirth)
                                    ->where('candidate_other_name', '=', $candidate_other_name)
                                    ->where('candidate_surname', '=', $candidate_surname)->first();
                                $candidate_no = (!$new_candidate) ? getNextCandidateNumber() : $new_candidate->candidate_no;
                                $data['candidate_no'] = $candidate_no;
                                $validation_rules['national_id'][] = Rule::unique('center_candidate')
                                    ->where(function ($query) use ($national_id, $level, $session) {
                                        return $query->where('national_id', $national_id)
                                            ->where('level', '=', $level)
                                            ->where('session', '=', $session)
                                            ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                                    });
                                if ($new_candidate) {
                                    $validation_rules['candidate_no'][] = Rule::unique('center_candidate')
                                        ->where(function ($query) use ($candidate_no, $level, $session) {
                                            return $query->where('candidate_no', $candidate_no)
                                                ->where('level', '=', $level)
                                                ->where('session', '=', $session)
                                                ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                                        });
                                }
                                $validator = Validator::make($data, $validation_rules, $validation_messages);
                                if ($validator->fails()) {
                                    $failed = true;
                                    $error = [
                                        'messages' =>  $validator->errors()->all(),
                                        'row' => $j + 1,
                                    ];
                                    array_push($errors, $error);
                                } else {

                                    // Assign Candidate Numberber
                                    if (!$new_candidate) {
                                        Candidate::create([
                                            'candidate_no' => $candidate_no,
                                            'national_id' => $national_id,
                                            'candidate_surname' => strtoupper($candidate_surname),
                                            'candidate_other_name' => strtoupper($candidate_other_name),
                                            'date_of_birth' => $dateOfBirth,
                                            'gender' => strtoupper($gender),
                                        ]);
                                    }
                                    // Subject validation start
                                    if ((($subject_number * 2) + 11) < $sponsor_col) {
                                        $i = 1;
                                        while ($i <= $subject_number) {
                                            $subjects['subjects'][] = array(
                                                'candidate_no' => $candidate_no,
                                                'subject_code' => (int)$candidates[$j][$subject_code],
                                                'subject_option' => $candidates[$j][$subject_option],
                                                'type' => $type
                                            );
                                            // $sponsor_col
                                            $subject_code  += 2;
                                            $subject_option +=  2;
                                            $i++;
                                        }
                                        $validation_rules = [
                                            'subjects' => ['required', new SubjectsGrouping($auth_center_no), new CheckDupsSubject(), 'max:' . $subject_number],
                                            'subjects.*' => ['required', new Extended()],
                                            'subjects.*.subject_code' =>  ['required', 'in:' . $subject_codes],
                                            'subjects.*.subject_option' =>   ['required', 'in:A,B']
                                        ];
                                        // The selected 2.subject_option is invalid.
                                        // The 3.subject_code field has a duplicate value.
                                        // duplicate value
                                        // is invalid.
                                        $validation_messages = [
                                            // add custom error messages
                                            // Subject code
                                            'subjects.0.subject_code.required' => "The 1st subject code is required",
                                            'subjects.1.subject_code.required' => "The 2nd subject code is required",
                                            'subjects.2.subject_code.required' => "The 3rd subject code is required",
                                            'subjects.3.subject_code.required' => "The 4th subject code is required",
                                            'subjects.4.subject_code.required' => "The 5th subject code is required",
                                            'subjects.5.subject_code.required' => "The 6th subject code is required",
                                            'subjects.6.subject_code.required' => "The 7th subject code is required",
                                            'subjects.7.subject_code.required' => "The 8th subject code is required",
                                            'subjects.8.subject_code.required' => "The 8th subject code is required",
                                            'subjects.9.subject_code.required' => "The 10th subject code is required",

                                            'subjects.0.subject_code.distinct' => "The 1st subject code has a duplicate value",
                                            'subjects.1.subject_code.distinct' => "The 2nd subject code has a duplicate value",
                                            'subjects.2.subject_code.distinct' => "The 3rd subject code has a duplicate value",
                                            'subjects.3.subject_code.distinct' => "The 4th subject code has a duplicate value",
                                            'subjects.4.subject_code.distinct' => "The 5th subject code has a duplicate value",
                                            'subjects.5.subject_code.distinct' => "The 6th subject code has a duplicate value",
                                            'subjects.6.subject_code.distinct' => "The 7th subject code has a duplicate value.",
                                            'subjects.7.subject_code.distinct' => "The 8th subject code has a duplicate value.",
                                            'subjects.8.subject_code.distinct' => "The 8th subject code has a duplicate value.",
                                            'subjects.9.subject_code.distinct' => "The 10th subject code has a duplicate value.",

                                            'subjects.0.subject_code.in' => "The 1st subject code is invalid.",
                                            'subjects.1.subject_code.in' => "The 2nd subject code is invalid.",
                                            'subjects.2.subject_code.in' => "The 3rd subject code is invalid.",
                                            'subjects.3.subject_code.in' => "The 4th subject code is invalid.",
                                            'subjects.4.subject_code.in' => "The 5th subject code is invalid.",
                                            'subjects.5.subject_code.in' => "The 6th subject code is invalid.",
                                            'subjects.6.subject_code.in' => "The 7th subject code is invalid.",
                                            'subjects.7.subject_code.in' => "The 8th subject code is invalid.",
                                            'subjects.8.subject_code.in' => "The 8th subject code is invalid.",
                                            'subjects.9.subject_code.in' => "The 10th subject code is invalid.",

                                            // Subject code
                                            'subjects.0.subject_option.required' => "The 1st subject option is required",
                                            'subjects.1.subject_option.required' => "The 2nd subject option is required",
                                            'subjects.2.subject_option.required' => "The 3rd subject optionis required",
                                            'subjects.3.subject_option.required' => "The 4th subject option is required",
                                            'subjects.4.subject_option.required' => "The 5th subject option is required",
                                            'subjects.5.subject_option.required' => "The 6th subject option is required",
                                            'subjects.6.subject_option.required' => "The 7th subject option is required",
                                            'subjects.7.subject_option.required' => "The 8th subject optionis required",
                                            'subjects.8.subject_option.required' => "The 8th subject optionis required",
                                            'subjects.9.subject_option.required' => "The 10th subject option is required",

                                            'subjects.0.subject_option.in' => "The 1st subject option is invalid.",
                                            'subjects.1.subject_option.in' => "The 2nd subject option is invalid.",
                                            'subjects.2.subject_option.in' => "The 3rd subject option is invalid.",
                                            'subjects.3.subject_option.in' => "The 4th subject option is invalid.",
                                            'subjects.4.subject_option.in' => "The 5th subject option is invalid.",
                                            'subjects.5.subject_option.in' => "The 6th subject option is invalid.",
                                            'subjects.6.subject_option.in' => "The 7th subject option is invalid.",
                                            'subjects.7.subject_option.in' => "The 8th subject option is invalid.",
                                            'subjects.8.subject_option.in' => "The 8th subject option is invalid.",
                                            'subjects.9.subject_option.in' => "The 10th subject option is invalid.",

                                        ];
                                        $validator = Validator::make($subjects, $validation_rules, $validation_messages);
                                        if ($validator->fails()) {
                                            $failed = true;
                                            $error = [
                                                'messages' =>  $validator->errors()->all(),
                                                'row' => $j + 1,
                                            ];

                                            array_push($errors, $error);
                                        }
                                    } else {
                                        $failed = true;
                                        $error = [
                                            'messages' => ['subject_number' => ['invalid number of subjects']],
                                            'row' => $j + 1,
                                        ];
                                        array_push($errors, $error);
                                    }
                                    // Subject validation End
                                }
                            } else {
                                $validation_rules['candidate_no'][] = Rule::unique('center_candidate')
                                    ->where(function ($query) use ($level, $candidate_no, $session) {
                                        return $query->where('candidate_no', $candidate_no)
                                            ->where('level', '=', $level)
                                            ->where('session', '=', $session)
                                            ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                                    });
                                $validation_rules['candidate_surname'][] = Rule::exists('candidates', 'candidate_surname')
                                    ->where('candidate_no', $candidate_no);
                                $validator = Validator::make($data, $validation_rules, $validation_messages);
                                if ($validator->fails()) {
                                    $failed = true;
                                    $error = [
                                        'messages' =>  $validator->errors()->all(),
                                        'row' => $j + 1,
                                    ];
                                    array_push($errors, $error);
                                } else {
                                    // Subject validation start
                                    if ((($subject_number * 2) + 11) < $sponsor_col) {
                                        $i = 1;
                                        while ($i <= $subject_number) {
                                            $subjects['subjects'][] = array(
                                                'candidate_no' => $candidate_no,
                                                'subject_code' => (int)$candidates[$j][$subject_code],
                                                'subject_option' => $candidates[$j][$subject_option],
                                                'type' => $type
                                            );
                                            // $sponsor_col
                                            $subject_code  += 2;
                                            $subject_option +=  2;
                                            $i++;
                                        }

                                        $validation_rules = [
                                            'subjects' => ['required', new SubjectsGrouping($auth_center_no), new CheckDupsSubject(), 'max:' . $subject_number],
                                            'subjects.*' => ['required', new Extended()],
                                            'subjects.*.subject_code' =>  ['required', 'in:' . $subject_codes],
                                            'subjects.*.subject_option' =>   ['required', 'in:A,B']
                                        ];
                                        // The selected 2.subject_option is invalid.
                                        // The 3.subject_code field has a duplicate value.
                                        // duplicate value
                                        // is invalid.
                                        $validation_messages = [
                                            // add custom error messages
                                            // Subject code
                                            'subjects.0.subject_code.required' => "The 1st subject code is required",
                                            'subjects.1.subject_code.required' => "The 2nd subject code is required",
                                            'subjects.2.subject_code.required' => "The 3rd subject code is required",
                                            'subjects.3.subject_code.required' => "The 4th subject code is required",
                                            'subjects.4.subject_code.required' => "The 5th subject code is required",
                                            'subjects.5.subject_code.required' => "The 6th subject code is required",
                                            'subjects.6.subject_code.required' => "The 7th subject code is required",
                                            'subjects.7.subject_code.required' => "The 8th subject code is required",
                                            'subjects.8.subject_code.required' => "The 8th subject code is required",
                                            'subjects.9.subject_code.required' => "The 10th subject code is required",

                                            'subjects.0.subject_code.distinct' => "The 1st subject code has a duplicate value",
                                            'subjects.1.subject_code.distinct' => "The 2nd subject code has a duplicate value",
                                            'subjects.2.subject_code.distinct' => "The 3rd subject code has a duplicate value",
                                            'subjects.3.subject_code.distinct' => "The 4th subject code has a duplicate value",
                                            'subjects.4.subject_code.distinct' => "The 5th subject code has a duplicate value",
                                            'subjects.5.subject_code.distinct' => "The 6th subject code has a duplicate value",
                                            'subjects.6.subject_code.distinct' => "The 7th subject code has a duplicate value.",
                                            'subjects.7.subject_code.distinct' => "The 8th subject code has a duplicate value.",
                                            'subjects.8.subject_code.distinct' => "The 8th subject code has a duplicate value.",
                                            'subjects.9.subject_code.distinct' => "The 10th subject code has a duplicate value.",

                                            'subjects.0.subject_code.in' => "The 1st subject code is invalid.",
                                            'subjects.1.subject_code.in' => "The 2nd subject code is invalid.",
                                            'subjects.2.subject_code.in' => "The 3rd subject code is invalid.",
                                            'subjects.3.subject_code.in' => "The 4th subject code is invalid.",
                                            'subjects.4.subject_code.in' => "The 5th subject code is invalid.",
                                            'subjects.5.subject_code.in' => "The 6th subject code is invalid.",
                                            'subjects.6.subject_code.in' => "The 7th subject code is invalid.",
                                            'subjects.7.subject_code.in' => "The 8th subject code is invalid.",
                                            'subjects.8.subject_code.in' => "The 8th subject code is invalid.",
                                            'subjects.9.subject_code.in' => "The 10th subject code is invalid.",

                                            // Subject code
                                            'subjects.0.subject_option.required' => "The 1st subject option is required",
                                            'subjects.1.subject_option.required' => "The 2nd subject option is required",
                                            'subjects.2.subject_option.required' => "The 3rd subject optionis required",
                                            'subjects.3.subject_option.required' => "The 4th subject option is required",
                                            'subjects.4.subject_option.required' => "The 5th subject option is required",
                                            'subjects.5.subject_option.required' => "The 6th subject option is required",
                                            'subjects.6.subject_option.required' => "The 7th subject option is required",
                                            'subjects.7.subject_option.required' => "The 8th subject optionis required",
                                            'subjects.8.subject_option.required' => "The 8th subject optionis required",
                                            'subjects.9.subject_option.required' => "The 10th subject option is required",

                                            'subjects.0.subject_option.in' => "The 1st subject option is invalid.",
                                            'subjects.1.subject_option.in' => "The 2nd subject option is invalid.",
                                            'subjects.2.subject_option.in' => "The 3rd subject option is invalid.",
                                            'subjects.3.subject_option.in' => "The 4th subject option is invalid.",
                                            'subjects.4.subject_option.in' => "The 5th subject option is invalid.",
                                            'subjects.5.subject_option.in' => "The 6th subject option is invalid.",
                                            'subjects.6.subject_option.in' => "The 7th subject option is invalid.",
                                            'subjects.7.subject_option.in' => "The 8th subject option is invalid.",
                                            'subjects.8.subject_option.in' => "The 8th subject option is invalid.",
                                            'subjects.9.subject_option.in' => "The 10th subject option is invalid.",

                                        ];
                                        $validator = Validator::make($subjects, $validation_rules, $validation_messages);

                                        if ($validator->fails()) {
                                            $failed = true;
                                            $error = [
                                                'messages' =>  $validator->errors()->all(),
                                                'row' => $j + 1,
                                            ];

                                            array_push($errors, $error);
                                        }
                                    } else {
                                        $failed = true;
                                        $error = [
                                            'messages' => ['subject_number' => ['invalid number of subjects']],
                                            'row' => $j + 1,
                                        ];
                                        array_push($errors, $error);
                                    }
                                    // Subject validation End
                                }
                            }
                        }
                        if (!$failed) {
                            $registerCandidate += 1;
                            $candidateProfileArray[] = [
                                'candidate_no' => $candidate_no,
                                'national_id' => $national_id
                            ];
                            $centerCandidateArray[] = [
                                'candidate_no' => $candidate_no,
                                'national_id' => $national_id,
                                'center_no' => $center_no,
                                'type' => $type,
                                'level' => $level,
                                'session' => $session,
                                'sponser' => $sponser,
                                'financial_year' => date('Y') . '-' . (date('Y') + 1),
                                'subject_number' => $subject_number,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s")
                            ];
                            $candidateUserArray[] = [
                                'national_id' => $national_id,
                                'candidate_no' =>  $candidate_no,
                                'center_no' => $center_no,
                                'username' => $candidate_surname . " " . $candidate_other_name,
                                'password' =>  Hash::make(str_replace('-', '', $dateOfBirth)),
                                'candidate_password' => str_replace('-', '', $dateOfBirth),
                                'session' => $session,
                                'financial_year' => date('Y') . '-' . (date('Y') + 1),
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s")
                            ];
                            foreach ($subjects['subjects'] as  $value) {
                                $subjectCandidateArray[] = [
                                    'national_id' => $national_id,
                                    'candidate_no' => $candidate_no,
                                    'subject_code' => $value['subject_code'],
                                    'subject_option' => $value['subject_option'],
                                    'level' => $level,
                                    'session' => $session,
                                    'financial_year' => date('Y') . '-' . (date('Y') + 1),
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s")
                                ];
                            }

                            insertOrUpdate('candidates',   $candidateProfileArray);
                            insertOrUpdate('center_candidate', $centerCandidateArray);
                            insertOrUpdate('candidate_users', $candidateUserArray);
                            insertOrUpdate('candidate_subject', $subjectCandidateArray);

                            $candidateProfileArray = array();
                            $centerCandidateArray = array();
                            $candidateUserArray = array();
                            $subjectCandidateArray = array();
                        }

                        $failed = true;
                        $validator = null;
                    }
                    break;
                case 'G7ELT':
                    $all_subjects = $center->subjects->pluck('subject_code');
                    $subject_number = count($all_subjects);
                    for ($j = 0; $j < count($candidates); $j++) {
                        $rownumber = $j;
                        $failed = false;

                        $candidate_subjects = ['subjects' => array()];
                        // Candidate is not find in Candidates Table Create New
                        $dateOfBirth = str_pad($candidates[$j][5], 8, '0', STR_PAD_LEFT);
                        $year   = substr($dateOfBirth, 4, 8);
                        $month  = substr($dateOfBirth, 2, 2);
                        $day = substr($dateOfBirth, 0, 2);

                        // Candidate
                        $center_no = $candidates[$j][0];
                        $national_id  = $candidates[$j][1];
                        $candidate_no  = $candidates[$j][2];
                        $candidate_surname  = strtoupper($candidates[$j][3]);
                        $candidate_other_name  = strtoupper($candidates[$j][4]);
                        $gender  = strtoupper($candidates[$j][6]);
                        $dateOfBirth = date("Y-m-d", mktime(0, 0, 0, $month,  $day, $year));

                        // Guardian
                        $guardian_national_id = time();
                        $guardian_surname  = strtoupper($candidates[$j][7]);
                        $guardian_name  = strtoupper($candidates[$j][8]);
                        $guardian_phone_number   = $candidates[$j][9];
                        $guardian_email   = "$auth_center_no@ecol.org.ls";
                        $guardian_village   = $candidates[$j][10];
                        $guardian_type   = 1;

                        $sponser = $candidates[$j][11];
                        $type = 1;
                        $data = [
                            'center_no' => $center_no,
                            'candidate_no' => $candidate_no,
                            'national_id' => $national_id,
                            'candidate_surname' =>  $candidate_surname,
                            'candidate_other_name' => $candidate_other_name,
                            'date_of_birth' => $dateOfBirth,
                            'gender' => $gender,
                            'type' => $type,
                            'sponser' => $sponser,
                            'subject_number' => $subject_number,
                            // Guardian
                            'guardian_national_id' => $guardian_national_id,
                            'guardian_surname'  => $guardian_surname,
                            'guardian_name'  => $guardian_name,
                            'guardian_phone_number'  => $guardian_phone_number,
                            'guardian_email'   => $guardian_email,
                            'guardian_type' => $guardian_type,
                            'guardian_village' => $guardian_village
                            //$center
                        ];
                        $validation_rules = [
                            'center_no' => ['required', 'in:' . $auth_center_no],
                            'candidate_no' => ['required'],
                            'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                            'candidate_surname' => ['required'],
                            'candidate_other_name' => ['required'],
                            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:-10 years'],
                            'gender' => ['required', 'in:M,F'],
                            'sponser' => ['required', 'in:M,O'],
                            'type' => ['required', 'in:1,2,3'],
                            'subject_number' => ['required', 'numeric'],
                            // Guardian
                            'guardian_national_id' => ['required', 'numeric'],
                            'guardian_surname'  => ['required'],
                            'guardian_name'  => ['required'],
                            'guardian_phone_number'  => ['required', 'numeric'],
                            'guardian_email'   => ['required', 'email'],
                            'guardian_type' => ['required'],
                            'guardian_village' => ['required']

                        ];

                        $validation_messages = [
                            // add custom error messages
                            'candidate_no.unique' => "The candidate is already registered",
                            'candidate_surname.exists' => "The candidate number is invalid",
                            'national_id.unique' => "The national ID is already registered",
                        ];
                        $validator = Validator::make($data, $validation_rules, $validation_messages);
                        if ($validator->fails()) {
                            $failed = true;
                            $error = [
                                'messages' =>  $validator->errors()->all(),
                                'row' => $j + 1,
                            ];
                            array_push($errors, $error);
                        } else {
                            // Check If require new candidate
                            if ($candidate_no == "*") {
                                $new_candidate = Candidate::whereDate('date_of_birth', $dateOfBirth)
                                    ->where('candidate_other_name', '=', $candidate_other_name)
                                    ->where('candidate_surname', '=', $candidate_surname)->first();
                                $candidate_no = (!$new_candidate) ? getNextCandidateNumber() : $new_candidate->candidate_no;
                                $data['candidate_no'] = $candidate_no;
                                $validation_rules['national_id'][] = Rule::unique('center_candidate')
                                    ->where(function ($query) use ($national_id, $level, $session) {
                                        return $query->where('national_id', $national_id)
                                            ->where('level', '=', $level)
                                            ->where('session', '=', $session)
                                            ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                                    });
                                if ($new_candidate) {
                                    $validation_rules['candiddate_no'][] = Rule::unique('center_candidate')
                                        ->where(function ($query) use ($candidate_no, $level, $session) {
                                            return $query->where('candiddate_no', $candidate_no)
                                                ->where('level', '=', $level)
                                                ->where('session', '=', $session)
                                                ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                                        });
                                }
                                $validator = Validator::make($data, $validation_rules, $validation_messages);
                                if ($validator->fails()) {
                                    $failed = true;
                                    $error = [
                                        'messages' =>  $validator->errors()->all(),
                                        'row' => $j + 1,
                                    ];
                                    array_push($errors, $error);
                                } else {

                                    // Assign Candidate Numberber
                                    if (!$new_candidate) {
                                        Candidate::create([
                                            'candidate_no' => $candidate_no,
                                            'national_id' => $national_id,
                                            'candidate_surname' => strtoupper($candidate_surname),
                                            'candidate_other_name' => strtoupper($candidate_other_name),
                                            'date_of_birth' => $dateOfBirth,
                                            'gender' => strtoupper($gender),
                                        ]);
                                    }
                                    foreach ($all_subjects as $subject_code) {
                                        $candidate_subjects['subjects'][] = array(
                                            'candidate_no' => $data['candidate_no'],
                                            'subject_code' => $subject_code,
                                            'subject_option' => "A",
                                            'type' => $type
                                        );
                                    }
                                }
                            } else {
                                $validation_rules['candidate_no'][] = Rule::unique('center_candidate')
                                    ->where(function ($query) use ($level, $candidate_no, $session) {
                                        return $query->where('candidate_no', $candidate_no)
                                            ->where('level', '=', $level)
                                            ->where('session', '=', $session)
                                            ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1));
                                    });
                                $validation_rules['candidate_surname'][] = Rule::exists('candidates', 'candidate_surname')
                                    ->where('candidate_no', $candidate_no);
                                $validator = Validator::make($data, $validation_rules, $validation_messages);
                                if ($validator->fails()) {
                                    $failed = true;
                                    $error = [
                                        'messages' =>  $validator->errors()->all(),
                                        'row' => $j + 1,
                                    ];
                                    array_push($errors, $error);
                                } else {

                                    foreach ($all_subjects as $subject_code) {
                                        $candidate_subjects['subjects'][] = array(
                                            'candidate_no' => $data['candidate_no'],
                                            'subject_code' => $subject_code,
                                            'subject_option' => "A",
                                            'type' => $type
                                        );
                                    }
                                }
                            }
                        }



                        if (!$failed) {
                            $registerCandidate += 1;
                            $centerCandidateArray[] = [
                                'candidate_no' => $candidate_no,
                                'national_id' => $national_id,
                                'center_no' => $center_no,
                                'type' => $type,
                                'level' => $level,
                                'session' => $session,
                                'sponser' => $sponser,
                                'financial_year' => date('Y') . '-' . (date('Y') + 1),
                                'subject_number' => $subject_number,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s")
                            ];
                            foreach ($candidate_subjects['subjects'] as  $value) {
                                $subjectCandidateArray[] = [
                                    'national_id' => $national_id,
                                    'candidate_no' => $candidate_no,
                                    'subject_code' => $value['subject_code'],
                                    'subject_option' => $value['subject_option'],
                                    'level' => $level,
                                    'session' => $session,
                                    'financial_year' => date('Y') . '-' . (date('Y') + 1),
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s")
                                ];
                            }


                            // Insert
                            insertOrUpdate('center_candidate', $centerCandidateArray);
                            insertOrUpdate('candidate_subject', $subjectCandidateArray);

                            //Address Candidate
                            Address::firstOrCreate(
                                ['user_id' => $national_id, 'user_type' => Candidate::class],
                                [
                                    "postal_address" => $guardian_village,
                                    "physical_address" => $guardian_village,
                                    "village" => $guardian_village,
                                    "user_id" => $national_id,
                                    "user_type" => Candidate::class,
                                    "district_code" => $center->district_code,
                                    "district" => $center->district,
                                ]
                            );
                            // Guardian Infomation
                            $guardian = Guardian::firstOrCreate(
                                [
                                    'candidate' => $candidate_no,
                                    'phone_number' => $guardian_phone_number
                                ],
                                [
                                    "candidate" => $candidate_no,
                                    'national_id' => $guardian_national_id,
                                    "guardian_type" => $guardian_type,
                                    "name" => $guardian_name,
                                    "surname" => $guardian_surname,
                                    "email" => $guardian_email,
                                    "phone_number" => $guardian_phone_number
                                ]
                            );
                            // Guardian addresss
                            Address::updateOrCreate(
                                ['user_id' => $guardian->national_id, 'user_type' => Guardian::class],
                                [
                                    "postal_address" => $guardian_village,
                                    "physical_address" => $guardian_village,
                                    "village" => $guardian_village,
                                    "user_id" => $guardian->national_id,
                                    "user_type" => Guardian::class,
                                    "district_code" => $center->district_code,
                                    "district" => $center->district,
                                ]
                            );
                            // Reset Array
                            $centerCandidateArray = array();
                            $candidate_subjects = ['subjects' => array()];
                            $centerCandidateArray = array();
                            $subjectCandidateArray = array();
                        }
                        $failed = true;
                        $validator = null;
                    }

                    break;
                default:
                    break;
            }
            $candidatesNumbers = [
                'registerCandidate' => $registerCandidate,
                'totalCandidates' => $totalCandidates,
                'unregisterCandidate' => $totalCandidates - $registerCandidate
            ];
            return response()->json(['errors' => $errors, 'candidatesNumbers' => $candidatesNumbers]);
        } catch (\Exception $e) {

            $error = [
                'messages' => ['format' => ['invalid format']],
                'row' =>  $rownumber + 1,
            ];
            array_push($errors, $error);
            $candidatesNumbers = [
                'registerCandidate' => $registerCandidate,
                'totalCandidates' => $totalCandidates,
                'unregisterCandidate' => $totalCandidates - $registerCandidate
            ];
            return response()->json(['errors' => $errors, 'candidatesNumbers' => $candidatesNumbers]);
        }
    }
    private   function csvToArray($file, $delimiter = ',')
    {
        $center_no = auth()->user()->center_no;
        $data = array_map(function ($l) use ($delimiter) {
            return  str_getcsv($l, $delimiter);
        },  $file);

        foreach ($data as $key => $value) {
            if (!Str::startsWith($value[0], $center_no)) {
                unset($data[$key]);
            }
        }
        $data = array_values($data);

        return  $data;
    }

    private function getFileDelimiter($file, $checkLines = 2)
    {
        $file = new \SplFileObject($file);
        $delimiters = array(
            ',',
            '\t',
            ';',
            '|',
            ':'
        );
        $results = array();
        $i = 0;
        while ($file->valid() && $i <= $checkLines) {
            $line = $file->fgets();
            foreach ($delimiters as $delimiter) {
                $regExp = '/[' . $delimiter . ']/';
                $fields = preg_split($regExp, $line);
                if (count($fields) > 1) {
                    if (!empty($results[$delimiter])) {
                        $results[$delimiter]++;
                    } else {
                        $results[$delimiter] = 1;
                    }
                }
            }
            $i++;
        }
        $results = array_keys($results, max($results));
        return $results[0];
    }
    private function checkKeyExists(array $arr, $key)
    {
        // is in base array?
        if (array_key_exists($key, $arr)) {
            return true;
        }
        // check arrays contained in this array
        foreach ($arr as $element) {
            if (is_array($element)) {
                if ($this->checkKeyExists($element, $key)) {
                    return true;
                }
            }
        }

        return false;
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
    public function is_registered($id)
    {
        $center_no = auth()->user()->center_no;
        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.center_no',
                    'centers.center_name',
                    'center_candidate.id',
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
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
            ->where("center_candidate.id", '=', $id)
            ->where('center_candidate.center_no', '=', $center_no)
            ->first();

        if ($candidate) {
            return  $candidate;
        } else {
            return false;
        }
    }
}
