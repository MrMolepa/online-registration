<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use App\Libraries\Payment\Payment;
use App\Mail\CentreInvoiceMail;
use App\Models\BankStatement;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidate;
use App\Models\CandidatePaymentConfirmation;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\CenterPaymentConfirmation;
use App\Models\Invoice;

use App\Models\Payment as PaymentModel;
use App\Models\SubjectCandidate;

use App\Rules\CheckDupsSubject;
use App\Rules\Extended;
use App\Rules\SubjectsGrouping;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PaymentVerificationController extends Controller
{

    // Payment Verification  centers
    public function index(Request $request)
    {
        $totalCandidates = CandidatePaymentConfirmation::query()->whereYear('created_at', date('Y'))->count();
        //  $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1)
        $totalRegisteredCandidates = CandidatePaymentConfirmation::query()->whereHas('candidate', function ($query) {
            $query->whereYear('created_at', date('Y'))
                ->where('financial_year', '=', (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1));
        })
            ->whereYear('created_at', date('Y'))->get()->count();
        $totalUnregisteredCandidates = $totalCandidates -   $totalRegisteredCandidates;

        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');

        $centers  = Center::get();

        // $this->removeEmptySubFolders(public_path("storage/bankStatement"));

        if ($request->ajax()) {
            $centers = Center::query()->whereHas('candidates', function ($query) use ($request) {
                $query->where('financial_year', '=', $request->year);
            });
            return   DataTables::eloquent($centers)
                ->addColumn('actions', function ($model) {
                    $financial_year = "";
                    if (date('m') <= 2) { //Upto June 2014-2015
                        $financial_year = (date('Y') - 1) . '-' . date('Y');
                    } else { //After June 2015-2016
                        $financial_year = date('Y') . '-' . (date('Y') + 1);
                    }
                    $uncheck = CenterPaymentConfirmation::where('checked_status', '=', '0')
                        ->where('center_id', '=', $model->center_no)
                        ->where('financial_year', '=', $financial_year)
                        ->count();
                    $htmlUncheck = ($uncheck > 0) ? "<span class='label label-danger'>$uncheck</span>" : "";
                    $actions = '<div class="btn-group actions">';
                    $actions .= "<a href='" . route('admin.payments-verification.center', $model->center_no) . "'
                                data-toggle='tooltip' title='Proof of payments'
                                class='btn btn-primary'><i class='fas fa-file-invoice-dollar'></i>
                                $htmlUncheck
                             </a>";
                    $actions .= "<a href='" . route('admin.center-charges.index', ['center_no' => $model->center_no]) . "'
                                data-toggle='tooltip' title='Charges'
                                class='btn btn-info'><i class='fas fa-money-bill'></i>
                            </a>";
                    $actions .= '</div>';
                    return       $actions;
                })->rawColumns(['actions'])
                ->toJson();
        }

        return view('admin.finance.payments-verification.payments-verification', compact(
            'totalCandidates',
            'totalRegisteredCandidates',
            'totalUnregisteredCandidates',
            'years',
            'centers'
        ));
    }



    // Payment Verification  candidates
    public function privateCandidates()
    {
        $privateCandidates = DB::table('candidate_confirmation')
            ->select(
                'candidate_confirmation.candidate_no',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidate_confirmation.id',
                'candidate_confirmation.bank_ref',
                'candidate_confirmation.candidate_info',
                'candidate_confirmation.amount',
                'candidate_confirmation.checked_by',
                'candidate_confirmation.checked_status',
                'candidate_confirmation.created_at',
                'candidate_confirmation.bank_confirmation',
                'center_candidate.level',
                'center_candidate.session',
                'center_candidate.center_no'
            )
            ->join('candidates', 'candidate_confirmation.candidate_no', '=', 'candidates.candidate_no')

            ->leftJoin('center_candidate', function ($join) {
                $join->on('candidate_confirmation.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_confirmation.candidate_info->level', '=', 'center_candidate.level');
                $join->on('candidate_confirmation.candidate_info->Session', '=', 'center_candidate.session');
                $join->on(DB::raw('YEAR(candidate_confirmation.created_at)'), '=', DB::raw('YEAR(center_candidate.created_at)'));
            })
            // ->join('center_candidate', 'center_candidate.candidate_no', '=', 'candidate_confirmation.candidate_no', 'left outer')
            ->orderBy('candidate_confirmation.checked_status', 'asc')
            ->whereYear('candidate_confirmation.created_at', '=', (date('Y') - 1))
            // ->where('candidate_info->Session', 'June')
            ->get();
        return DataTables::of($privateCandidates)
            ->editColumn('candidate_no', function ($model) {
                return   str_pad($model->candidate_no, 9, '0', STR_PAD_LEFT);
            })
            ->addColumn('candidate_information', function ($model) {
                return  json_decode($model->candidate_info, true);
            })
            ->addColumn('bank_confirmation_path', function ($model) {
                $files = $this->addFiles($model->candidate_no);
                return json_decode($files, true);
            })
            ->addColumn('candidate_subjects', function ($model) {
                return $this->getCandidateSubjects(json_decode($model->candidate_info));
            })
            ->editColumn('bank_statements', function ($model) {
                $output = "";
                $files = $this->addFiles($model->candidate_no);

                // $deleted=  $this->deleteFiles($model->candidate_no);

                foreach ($files as $path) {
                    $output .=  '<a href="' . asset($path->file_storage) . '" data-toggle="tooltip"
                                title="' . $path->last_modified . '" target="_blank"
                                class="btn btn-primary"><i class="fas fa-download" download></i>
                            </a>';
                }
                return $output;
            })

            ->editColumn('actions', function ($model) {
                $actions = '<div class="btn-group actions">';
                $actions .= "<a href='javascript:void(0)'
                                data-toggle='tooltip' title='Check'  data-url='" . route('admin.payments-verification.candidate.edit', $model->id) . "'  class='btn btn-primary  btn-edit-check'>
                                <i class='fas fa-check-square'></i>
                            </a>";
                $actions .= " <a href='javascript:void(0)' data-url='" . route('admin.payments-verification.comments.edit', $model->id) . "'
                                    data-toggle='tooltip' title='Comments' class='btn btn-info btn-edit-comment'> <i
                                        class='fas fa-comments'></i>
                                </a>";
                $actions   .=  "<a href='javascript:void(0)' class='delete-candidate btn btn-danger' data-url='" . route('admin.payments-verification.candidate.delete', $model->id) . "'  type='button' rel='tooltip' title='Delete'>
                                <i class='far fa-trash-alt'></i>
                        </a>";
                $actions .= '</div>';
                return    $actions;
            })
            ->addColumn('checked_status', function ($model) {
                $output = "";
                if ($model->checked_status == 1) {
                    $output .= '<span class="invalid-status"></span>';
                } else if ($model->checked_status == 2) {
                    $output .= '<span class="valid-status"></span>';
                } else {
                    $output .= '<span class="not-checked-status"></span>';
                }
                return $output;
            })->rawColumns(['checked_status', 'bank_statements', 'actions'])
            ->make(true);
    }

    private function deleteFiles($candidate_no)
    {
        $candidate_no = str_pad($candidate_no, 9, '0', STR_PAD_LEFT);
        $files = Storage::files("public/bankStatement/$candidate_no");
        $count = 1;
        foreach ($files as $file) {
            $name = explode('/', $file)[3];
            $lastmodified = Storage::lastModified($file);
            $lastmodified = date("Y-m-d H:i:s", $lastmodified);
            $lastmodifiedYear = date('Y', strtotime($lastmodified));
            if (date('Y') - 1 == $lastmodifiedYear) {
                if (File::exists(public_path("storage/bankStatement/$candidate_no/$name"))) {
                    File::delete(public_path("storage/bankStatement/$candidate_no/$name"));
                    $count++;
                }
            }
        }
        return  $count++;
    }
    // Payment Verification  center proof payments
    public function center_proof_payments(Request $request, $center_no)
    {

        if ($request->ajax()) {
            $confirmations = CenterPaymentConfirmation::with('center', 'user')
                ->where('center_id', '=', $center_no)
                ->where('financial_year', '=', $request->year)->get();
            return DataTables::of($confirmations)
                ->editColumn('status', function ($row) {
                    $status = "";
                    if ($row->checked_status == 1) {
                    } elseif ($row->checked_status == 2) {
                        $status = "<span class='valid-status'></span>";
                    } else {
                        $status = "<span class='not-checked-status'></span>";
                    }
                    return  "$status  $row->created_at";
                })
                ->editColumn('center_no', function ($row) {
                    return    $row->center_id;
                })
                ->editColumn('center_name', function ($row) {
                    return  $row->center->center_name;
                })
                ->editColumn('confirmation_slip', function ($row) {
                    $confirmation_slip = "";
                    if (!is_null($row->bank_statement_path)) {
                        $confirmation_slip = "<a href='" . asset($row->bank_statement_path) . "'
                             target='_blank'><i class='fas fa-download'  class='btn btn-primary' download></i></a>";
                    } else {
                        $confirmation_slip = " <p>Bal b/f</p>";
                    }
                    return  $confirmation_slip;
                })
                ->editColumn('amount_paid', function ($row) {
                    return   $row->amount_paid;
                })
                ->editColumn('email', function ($row) {
                    return     is_null($row->user) ? ' ' : $row->user->email;
                })
                ->editColumn('actions', function ($row) {
                    $html = "<button type='button' title='edit' class='btn btn-sm btn-primary editBtn'  data-url='" . route('admin.payments-verification.center.edit', $row->id) . "' > Edit</button>
                         <button data-url='" . route('admin.payments-verification.center.delete', $row->id) . "' class='btn btn-sm btn-danger deleteBtn'> Delete </button>
                         ";
                    return     $html;
                })
                ->rawColumns(['status', 'center_no', 'center_name', 'confirmation_slip', 'amount_paid', 'email', 'actions'])
                ->toJson();
        }



        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
        $total_paid = CenterPaymentConfirmation::with('center', 'user')
            ->where('financial_year', '=',   $years[0])
            ->where('center_id', '=', $center_no)
            ->sum('amount_paid');

        $schoolfees = Payment::schoolfees($center_no, null, "November", $years[0]);

        $center = Center::where('center_no', '=', $center_no)->first();
        return view('admin.finance.payments-verification.center.center-verification', compact('center', 'total_paid', 'schoolfees', 'years'));
    }

    public function centerCharges(Request $request, $center_no)
    {
        if ($request->ajax()) {

            $levels = DB::table('center_candidate')->select(
                [
                    'center_candidate.level'
                ],
            )
                ->distinct()
                ->orderBy('level')
                ->where('financial_year', '=', $request->year)
                ->where('center_no', '=', $center_no)
                ->get();


            $html = '';
            $total_paid = 0;
            $total_charges = 0;
            $total_overdue=0;


            $html = "<table class='table' name='tablename'>
                         <thead>

                        </thead>
                        <tbody>";


            foreach ($levels as $level) {
                $schoolfees = Payment::schoolfees($center_no, $level->level, "November", $request->year);
                $level_overdue = $schoolfees['sponsors']['O']['sponsor_overdue'] + $schoolfees['sponsors']['P']['sponsor_overdue'];
                $total_overdue += $level_overdue;
                $level_total_paid = $schoolfees['total_paid'];
                $level_charges = $schoolfees['other_charges'];

                if ($total_paid< $level_total_paid) {
                    $total_paid= $level_total_paid;
                }
                if ( $total_charges<  $level_charges) {
                    $total_charges=  $level_charges;
                }


                $html .= "<tr>
                             <th colspan='7'>$level->level </th>
                           </tr>
                            <tr>
                                <th colspan='4'>Total Fees</th>
                                <td colspan='3'>
                                    LSL " . number_format($level_overdue, 2, '.', '') . "
                                </td>
                            </tr>

                            ";
            }

            $balance   = $total_overdue +  $total_charges - $total_paid;

            $html .= "
                      <tr>
                         <th colspan='4'>Total Overdue</th>
                            <td colspan='3'>
                                LSL " . number_format( $total_overdue, 2, '.', '') . "
                            </td>
                        </tr>
                        <tr>
                         <th colspan='4'>Total Paid</th>
                            <td colspan='3'>
                                LSL " . number_format( $total_paid, 2, '.', '') . "
                            </td>
                        </tr>
                        <tr>
                         <th colspan='4'>Other Charges</th>
                            <td colspan='3'>
                                LSL " . number_format( $total_charges, 2, '.', '') . "
                            </td>
                        </tr>
                         <tr>
                         <th colspan='4'>Balance</th>
                            <td colspan='3'>
                                LSL " . number_format(   $balance, 2, '.', '') . "
                            </td>
                        </tr>
                        </tbody>
                        </table>";

            return response()->json(['html' => $html]);
        }
    }

    // Payment edit  center proof payments
    public function editProofPaymentCenter($id)
    {

        $confirmation = CenterPaymentConfirmation::with('center')->findOrFail($id);
        $url = route('admin.payments-verification.center.update', $id);
        return response()->json(['confimation' => $confirmation, 'url' => $url]);
    }
    // Payment update  center proof payments update
    public function updateProofPaymentCenter(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'amount_paid' => 'required',
            'bank_ref' => 'required|unique:bank_statements,bank_ref,' . $id,
            'confirmation' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $confirmation = CenterPaymentConfirmation::findOrFail($id);
        $confirmation->amount_paid = $request->amount_paid;
        $confirmation->bank_ref = $request->bank_ref;
        $confirmation->checked_status = $request->confirmation;
        $confirmation->financial_year = $request->financial_year;
        $confirmation->checked_by = auth()->user()->id;
        $confirmation->save();
        // Send Email To the Center
        $center = Center::where('center_no', '=', $request->center_no)->first();
        if ($request->confirmation == 2) {
            $total_paid = CenterPaymentConfirmation::with('center', 'user')
                ->where('financial_year', '=',  $confirmation->financial_year)
                ->where('center_id', '=', $center->center_no)->sum('amount_paid');
            $schoolfees = Payment::schoolfees($center->center_no, null, "November",  $confirmation->financial_year);
            $balance = $schoolfees['total_charge'] + $schoolfees['sponsor'][2] + $schoolfees['bank_charge'] - $total_paid;
            $amount_paid = $request->amount_paid;
            try {
                Mail::to($confirmation->email)
                    ->send(new CentreInvoiceMail($center, $schoolfees, $total_paid, $amount_paid, $balance));
                return response()->json(['success' => 'Successfully added the records and send email']);
            } catch (Exception $ex) {
                // Debug via $ex->getMessage();
                return response()->json(['success' => 'Successfully added the records and email was not sent']);
            }
        }
        // End
        return response()->json(['success' => 'Successfully added the records and email was not sent']);
    }

    // Payment delete  center proof payments update
    public function deleteProofPaymentCenter($id)
    {
        $confirmation = CenterPaymentConfirmation::with('center')->findOrFail($id);
        if (File::exists(public_path($confirmation->bank_statement_path))) {
            File::delete(public_path($confirmation->bank_statement_path));
        }
        $confirmation->delete();
        return response()->json(['success' => 'Successfully deleted the records']);
    }


    public function addBalanceBroughtForward(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount_paid' => 'required',
            'bank_reference' => ['required', "unique:bank_statements,bank_ref"]
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $bankStatement = new BankStatement();
        $bankStatement->center_id = $request->center_no;
        $bankStatement->amount_paid = $request->amount_paid;
        $bankStatement->bank_ref = $request->bank_reference;
        $bankStatement->financial_year = $request->financial_year;
        $bankStatement->checked_status = 2;
        $bankStatement->checked_by = auth()->user()->id;
        $bankStatement->save();
        // End
        return response()->json(['success' => 'Successfully added the records']);
    }
    //Payment Edit  candidates proof payments
    public function editProofPaymentCandidate($id)
    {

        $candidate = DB::table('candidate_confirmation')
            ->select(
                'candidates.candidate_no',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidate_confirmation.id',
                'candidate_confirmation.bank_ref',
                'candidate_confirmation.amount',
                'candidate_confirmation.checked_by',
                'candidate_confirmation.checked_status',
                'candidate_confirmation.created_at',
                'candidate_confirmation.bank_confirmation_path',
                'candidate_confirmation.bank_confirmation'
            )
            ->join('candidates', 'candidate_confirmation.candidate_no', '=', 'candidates.candidate_no')
            ->where('candidate_confirmation.id', '=', $id)
            ->first();
        $url = route('admin.payments-verification.candidate.update', $id);
        return response()->json(['candidate' => $candidate, 'url' => $url]);
    }

    public function updateProofPaymentCandidate(Request $request, $id)
    {
        $candidate =  DB::table('candidate_confirmation')->where('id', '=', $id)->first();
        $candidateInfo = json_decode($candidate->candidate_info);
        $msg = "";
        $status = 0;
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|in:' . $candidateInfo->total_amount,
            'bank_reference' => 'required|unique:candidate_confirmation,bank_ref,' . $id . '|unique:invoices,reference_no',
            'confirmation' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $user_email = auth()->user()->email;
        DB::table('candidate_confirmation')->where('id', '=', $id)->update(
            array(
                'checked_status' => $request->confirmation,
                'checked_by'   =>  $user_email,
                'bank_ref'   => $request->bank_reference,
                'amount'   => $request->amount
            )
        );
        if ($request->confirmation == 2) {
            $candidateRequest = new Request();
            $candidateRequest->replace(json_decode($candidate->candidate_info, true));
            if (is_bool($this->registerCandidate($candidateRequest, $id))) {
                // Send Email To the Candidate


                $body = "Dear {$candidateInfo->surname} {$candidateInfo->other_name} ({$candidateInfo->candidateNo})
                     <br><br>
                     This is to confirm  that your proof of payment with  Examinations Council of Lesotho online Registration
                     was successfully for LSL{$candidateInfo->total_amount}
                     <br>
                     -----------------------------------------------------------
                     Level  : {$candidateInfo->level}
                     -----------------------------------------------------------
                     <br>
                     -----------------------------------------------------------
                     Session  : {$candidateInfo->Session}
                     -----------------------------------------------------------
                     <br>
                     -----------------------------------------------------------
                     Total Subject(s)  : {$candidateInfo->number_of_subjects}
                     -----------------------------------------------------------
                     <br>
                     -----------------------------------------------------------
                     Total Amount  : {$candidateInfo->total_amount}
                     -----------------------------------------------------------
                     <br>
                      Please download your timetable at  https://register.examscouncil.org.ls
                     <br>
                     -->Enter Candidate Number
                     <br>
                     -->Select Registered Session
                     <br>
                     -->Download timetable
                    <br>
                     Thank you
                     <br>
                     Examinations Council of Lesotho
                     <br><br>For further assistance concerning registration, please contact us :<br>
                     +26622312880 / +2665300132 / +2665300100 examscouncil@examscounicil.org.ls";
                $subject = "Examinations Council of Lesotho proof of payment";
                if ($this->sendConfirmation($candidateInfo->email_Address, $subject, $body)) {
                    $msg = "Registered successfully  and  sent  email confirmation";
                    $status = 1;
                    return response()->json(['errors' => $validator->errors(), 'status' => $status, 'msg' => $msg]);
                }
                $msg = "Candidate  successfully registered confirmation email was not  sent";
                $status = 1;
                return response()->json(['errors' => $validator->errors(), 'status' => $status, 'msg' => $msg]);
                // End
            }

            $msg = "Candidate was not successfully registered";
            $status = 0;
            return response()->json(['errors' => $validator->errors(), 'status' => $status, 'msg' => $msg]);
        }
        $msg = "Candidate was not successfully registered";
        return response()->json(['errors' => $validator->errors(), 'status' => $status, 'msg' => $msg]);
    }


    public function deleteProofPaymentCandidate($id)
    {
        $msg = "";
        $status = 0;
        $candidateConfirmadion =  DB::table('candidate_confirmation')->where('id', '=', $id)->first();
        $candidate_no = str_pad($candidateConfirmadion->candidate_no, 9, '0', STR_PAD_LEFT);
        if (File::deleteDirectory(public_path("storage/bankStatement/$candidate_no/"))) {
            DB::table('candidate_confirmation')->where('id', '=', $id)->delete();
            $msg = "Successfully deleted the records";
            $status = 1;
            return response()->json(['status' => $status, 'msg' => $msg]);
        }
        $msg = "Candidate was not deleted";
        return response()->json(['status' => $status, 'msg' => $msg]);
    }





    public function  removeImage(Request $request)
    {
        $candidate_no = str_pad($request->candidate_no, 9, '0', STR_PAD_LEFT);
        if (File::exists(public_path("storage/bankStatement/$candidate_no/$request->file_name"))) {
            File::delete(public_path("storage/bankStatement/$candidate_no/$request->file_name"));
            return response()->json(['success' => 'Successfully deleted extra image']);
        }
        return response()->json(['success' => 'File does not exist']);
    }

    // comments Edit
    public function editComments($id)
    {
        $privateCandidate = DB::table('candidate_confirmation')
            ->select(
                'candidates.candidate_no',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidate_confirmation.id',
                'candidate_confirmation.comments',
                'candidate_confirmation.bank_ref',
                'candidate_confirmation.amount',
                'candidate_confirmation.checked_by',
                'candidate_confirmation.checked_status',
                'candidate_confirmation.created_at',
                'candidate_confirmation.bank_confirmation_path',
                'candidate_confirmation.bank_confirmation'
            )
            ->join('candidates', 'candidate_confirmation.candidate_no', '=', 'candidates.candidate_no')
            ->where('candidate_confirmation.id', '=', $id)
            ->first();
        $candidate = CandidatePaymentConfirmation::findOrFail($id);
        $candidateInfo = json_decode($candidate->candidate_info);
        $url = route('admin.payments-verification.comments.update', $id);

        return response()->json([
            'privateCandidate' => $privateCandidate,
            'candidateInfo' => $candidateInfo,
            'url' => $url
        ]);
    }
    // comments update
    public function updateComments(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'comments' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $msg = " ";
        $status = 0;
        $candidate = CandidatePaymentConfirmation::findOrFail($id);
        $candidateInfo = json_decode($candidate->candidate_info);
        $user_email = auth()->user()->email;
        DB::table('candidate_confirmation')->where('id', '=', $id)->update(
            array(
                'checked_status' => 1,
                'checked_by'   =>  $user_email,
                'comments'   => $request->comments,

            )
        );

        // Send Email To the Candidate
        $body = "Dear {$candidateInfo->surname} {$candidateInfo->other_name} ({$candidateInfo->candidateNo})
            <br><br>
            {$request->comments}
            <br>
            Thank you
            <br>
            Examinations Council of Lesotho
            <br><br>For further assistance concerning registration, please contact us :<br>
            +26622312880 / +2665300132 / +2665300100 examscouncil@examscounicil.org.ls";
        $subject = "Examinations Council of Lesotho proof of payment";
        if ($this->sendConfirmation($candidateInfo->email_Address, $subject, $body)) {
            $msg = "Successfully send  email confirmation   to the candidate";
            $status = 1;
        }
        $msg = "Email  confirmation  was not sended";
        return response()->json(['errors' => $validator->errors(), 'status' => $status, 'msg' => $msg]);
        // End



    }

    private function sendConfirmation($email, $subject, $body)
    {
        if (!is_null($email)) {
            // Instantiation and passing `true` enables exceptions
            require base_path("vendor/autoload.php");
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
                //   $mail->Mailer = “smtp”;
                $mail->Username   = 'noreply@examscouncil.org.ls';                     // SMTP username
                $mail->Password   = 'Ec0l.OTP2020@';                               // SMTP password
                $mail->Port = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above


                //Recipients
                $mail->setFrom('noreply@examscouncil.org.ls', 'Examinations Council of Lesotho');
                // sender
                $mail->addAddress($email);
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    = $body;
                $mail->AltBody = strip_tags($body);
                if ($mail->send()) {

                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return $e;
            }
        }
        return false;
    }
    public function paginate($items, $perPage = 5, $page = null, $option = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page'
        ]);
    }

    private function addFiles($candidate_no)
    {
        $candidate_no = str_pad($candidate_no, 9, '0', STR_PAD_LEFT);
        $files = Storage::files("public/bankStatement/$candidate_no");
        $backups = new Collection();
        foreach ($files as $file) {
            $size = Storage::size($file);
            $name = explode('/', $file)[3];
            $lastmodified = Storage::lastModified($file);
            $lastmodified = date("Y-m-d H:i:s", $lastmodified);
            $mimeType = Storage::mimeType($file);
            $mimeTypes = ["image/jpeg", "image/jpg", "image/png"];
            $lastmodifiedYear = date('Y', strtotime($lastmodified));
            if (date('Y') == $lastmodifiedYear && in_array($mimeType, $mimeTypes)) {
                $backups->push((object) [
                    'file_path' => $file,
                    'file_storage' => "/storage/bankStatement/$candidate_no/$name",
                    'file_name' => $name,
                    'file_size' => $this->humanReadable($size),
                    'last_modified' =>  $lastmodified,
                ]);
            } else {
                if (File::exists(public_path("storage/bankStatement/$candidate_no/$name"))) {
                    File::delete(public_path("storage/bankStatement/$candidate_no/$name"));
                }
            }
        }
        return  $backups;
    }
    private function humanReadable($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
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
        $bankconfirmation =   DB::table('candidate_confirmation')
            ->where('candidate_no', '=', $request->get('candidate_no'))
            ->whereIn('checked_status', [0, 1])
            ->count();
        if ($bankconfirmation > 0) {
            return response()->json(['errors' => ['candidate_no' => ['candidate already registerd']]]);
        } else {
            if (count($candidate_registration) == 1) {
                $candidate_registration = $candidate_registration->first();
                $session_html = $candidate_registration->session == "November"  ? "<option value='June'>June</option>" : "<option value='November'>November</option>";
                $output =  "<div class='row'>
                                <div class='form-group col-md-3'>
                                    <label for=' '>Candidate Number  </label>
                                    <input type='text' class='form-control' name='candidate_number' id='inputEmail4' readonly value='" . str_pad($candidate->candidate_no, 9, '0', STR_PAD_LEFT) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-3'>
                                    <label for='surname'>Surname</label>
                                    <input type='text' class='form-control' name='surname' id='surname' readonly value='" . htmlspecialchars($candidate->candidate_surname, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-3'>
                                    <label for='other_name'>Other name</label>
                                    <input type='text' class='form-control' name='other_name' id='other_name' readonly value='" . htmlspecialchars($candidate->candidate_other_name, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-3'>
                                    <label for=' '>Date of birth</label>
                                    <input type='text' class='form-control' id='date_of_birth' readonly value='$candidate->date_of_birth'>
                                    <span class='help-block'></span>
                                </div>
                        </div>
                        <div class='row'>
                                <div class='form-group col-md-4'>
                                    <label for='gender'>Gender</label>
                                    <input type='text' class='form-control' name='gender' id='gender' readonly value='  $candidate->gender'>
                                </div>
                                <div class='form-group col-md-4'>
                                    <label for='gender'>Email address </label>
                                    <input type='text' class='form-control' name='email' id='email'  value=''>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-4'>
                                    <label for='phone_number'>Phone number</label>
                                    <input type='text' class='form-control' name='phone_number' id='phone_number'  value=''>
                                    <span class='help-block'></span>
                                </div>
                        </div>
                        <div class='row'>
                        <div class='form-group col-md-12'>
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
                                <input type='checkbox' name='subject[]' value='0176,SESOTHO' id='sesotho'>
                                <label  for='sesotho'>SESOTHO (0176)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0198,PHYSICS' id='physics'>
                                <label  for='physics'>PHYSICS (0198)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0197,CHEMISTRY' id='chemistry'>
                                <label  for='chemistry'>CHEMISTRY (0197)</label>
                            </div>

                        </div>
                        <span class='help-block'></span>
                    </div>";
                $status = 1;
            } elseif (count($candidate_registration) == 2) {
                return response()->json(['errors' => ['candidate' => ['candidate already registerd']]]);
            } else {
                $output =  "<div class='row'>
                                <div class='form-group col-md-3'>
                                    <label for=' '>Candidate Number</label>
                                    <input type='text' class='form-control' name='candidate_number' id='inputEmail4' readonly value='" . str_pad($candidate->candidate_no, 9, '0', STR_PAD_LEFT) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-3'>
                                    <label for='surname'>Surname</label>
                                    <input type='text' class='form-control' name='surname' id='surname' readonly value='" . htmlspecialchars($candidate->candidate_surname, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-3'>
                                    <label for='other_name'>Other name</label>
                                    <input type='text' class='form-control' name='other_name' id='other_name' readonly value='" . htmlspecialchars($candidate->candidate_other_name, ENT_QUOTES) . "'>
                                    <span class='help-block'></span>
                                </div>
                                <div class='form-group col-md-3'>
                                    <label for='date_of_birth'>Date of birth</label>
                                    <input type='text' class='form-control' id='date_of_birth' name='date_of_birth' readonly value='$candidate->date_of_birth'>
                                    <span class='help-block'></span>
                                </div>
                        </div>
                        <div class='row'>
                            <div class='form-group col-md-4'>
                                <label for='gender'>Gender</label>
                                <input type='text' class='form-control' name='gender' id='gender' readonly value='$candidate->gender'>
                                <span class='help-block'></span>
                            </div>
                            <div class='form-group col-md-4'>
                                <label for='email'>Email Address</label>
                                <input type='text' class='form-control' name='email' id='email'  value=''>
                                <span class='help-block'></span>
                            </div>
                            <div class='form-group col-md-4'>
                                <label for='phone_No'>Phone Number</label>
                                <input type='text' class='form-control' name='phone_number' id='phone_number'  value=''>
                                <span class='help-block'></span>
                            </div>
                        </div>
                        <div class='row'>
                        <div class='form-group col-md-12'>
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
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0185,LITERATURE IN ENGLISH'  id='english_lit'>
                                <label  for='english_lit'>LITERATURE IN ENGLISH (0185)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='physcial_science[]' value='0181,PHYSCICAL-SCIENCE (Core)'  id='physical_science_core'>
                                <label  for='physical_science_core'>PHYSICAL SCIENCE (Core, 0181)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='physcial_science[]' value='0181,PHYSCICAL-SCIENCE (Extendend)'  id='physical_science_extended'>
                                <label  for='physical_science_extended'>PHYSICAL SCIENCE (Extendend, 0181)</label>
                            </div>

                            <div class='checkbox'>
                                <input type='checkbox' name='mathematics[]' value='0178,MATHEMATICS (Core)'  id='maths_core'>
                                <label  for='maths_core'>MATHEMATICS (Core, 0178)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='mathematics[]' value='0178,MATHEMATICS (Extendend)'  id='maths_extended'>
                                <label  for='maths_extended'>MATHEMATICS (Extendend, 0178)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0176,SESOTHO' id='sesotho'>
                                <label  for='sesotho'>SESOTHO (0176)</label>
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
                                <input type='checkbox' name='subject[]' value='0182,HISTORY'  id='development_studies'>
                                <label  for='development_studies'>DEVELOPMENT STUDIES (0182)</label>
                            </div>
                        </div>
                        <div class='form-group  col-md-4'>
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
                                <input type='checkbox' name='subject[]' value='0198,PHYSICS' id='physics'>
                                <label  for='physics'>PHYSICS (0198)</label>
                            </div>
                            <div class='checkbox'>
                                <input type='checkbox' name='subject[]' value='0197,CHEMISTRY' id='chemistr'>
                                <label  for='chemistry'>CHEMISTRY (0197)</label>
                            </div>
                        </div>
                        <span class='help-block'></span>
                    </div>";
                $status = 1;
            }
            return response()->json(['status' =>  $status, 'html' =>  $output]);
        }
    }


    public function storeCandidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_no' => 'required',
            'center_no' => 'required',
            'level' => 'required',
            'session' => 'required',
            'candidate_confirmation' => 'required|mimes:jpeg,png,jpg,gif',
            'email' => 'required|email',
            'phone_number' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $request->request->add(['type' => '3']);
        $all_subjects = $this->candidateSubjectValidation($request);
        $subject_number = count($all_subjects['subjects']);
        $validation_rules = [
            'subjects' => ['required', new SubjectsGrouping(), new CheckDupsSubject()],
            'subjects.*' => ['required', new Extended()],
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
        $validator = Validator::make($all_subjects, $validation_rules, $validation_messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $validator = null;
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $fees = DB::table('fees_stracture')
            ->where('candidate_type', '=', 'lgcse-private')
            ->where('financial_year', '=', $financial_year)
            ->first();
        $local_fee = $fees->local_fee;
        $registration_fee = $fees->registration_fee;
        $total_amount = ($subject_number * $fees->subject_fee) + $local_fee + $registration_fee + $fees->bank_charge;

        // Add Required
        $request->request->add(['candidateNo' => $request->candidate_no]);
        $request->request->add(['number_of_subjects' => $subject_number]);
        $request->request->add(['total_amount' => $total_amount]);
        $request->request->add(['email_Address' => $request->email]);
        $request->request->add(['phone_No' => $request->phone_number]);
        $request->request->add(['payment' => "CashDeposit"]);
        $request->request->add(['centreNo' => $request->center_no]);
        $request->request->add(['Session' => $request->session]);
        $request->request->add(['increaseSubjects' => null]);


        // Remove
        $request->request->remove('candidate_no');
        $request->request->remove('center_no');
        $request->request->remove('session');
        $request->request->remove('phone_number');
        $request->request->remove('email');
        $request->request->remove('_token');
        if ($request->file()) {
            $candidateNo = $request->candidateNo;
            $fileName =  $candidateNo  . '-' . time() . '-' . $request->candidate_confirmation->getClientOriginalName();
            $filePath = $request->candidate_confirmation->storeAs('bankStatement/' .  $candidateNo, $fileName, 'public');
            DB::table('candidate_confirmation')->insert(
                array(
                    'candidate_no' => $candidateNo,
                    'candidate_info'   =>   json_encode($request->all()),
                    'bank_confirmation'   => $fileName,
                    'bank_confirmation_path'   =>  '/storage/' . $filePath,
                )
            );
        }
        return response()->json(['status' => 1]);
    }

    private function getCandidateSubjects($candidate_info)
    {

        $all_subjects = [];
        if (isset($candidate_info->subject)) {
            $subjects = is_array($candidate_info->subject) ? explode(",", implode(",", $candidate_info->subject)) : explode(',', $candidate_info->subject);
            $keys = array_keys($subjects);
            $last = end($keys);
            foreach (range(0, $last - 1, -2) as $key) {
                $subject_code =  $subjects[$key];
                $subject_option = 'A';
                $all_subjects  += array($subject_code => $subject_option);
            }
        }
        if (isset($candidate_info->mathematics)) {
            $code = is_array($candidate_info->mathematics) ? explode(',', implode('', $candidate_info->mathematics))[0] : explode(',', $candidate_info->mathematics);
            $option = is_array($candidate_info->mathematics) ? explode(" ", explode(',', implode(' ', $candidate_info->mathematics))[1]) : explode(' ', $code[1]);
            $subject_code = is_array($candidate_info->mathematics) ?  $code :  $code[0];
            $subject_option = '';
            if ($option[1] == "(Core)") {
                $subject_option = "A";
            } else {
                $subject_option = "B";
            }
            $all_subjects += array($subject_code => $subject_option);
        }
        if (isset($candidate_info->physcial_science)) {
            $code = is_array($candidate_info->physcial_science) ? explode(',', implode('', $candidate_info->physcial_science))[0] : explode(',', $candidate_info->physcial_science);
            $option = is_array($candidate_info->physcial_science) ? explode(" ", explode(',', implode(' ', $candidate_info->physcial_science))[1]) : explode(' ', $code[1]);
            $subject_code = is_array($candidate_info->physcial_science) ?  $code :  $code[0];
            $subject_option = '';
            if ($option[1] == "(Core)") {
                $subject_option = "A";
            } else {
                $subject_option = "B";
            }
            $all_subjects += array($subject_code => $subject_option);
        }
        return   $all_subjects;
    }

    private function registerCandidate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'candidateNo' => 'required',
            'payment' => 'required',
            'Session' => 'required',
            'level' => 'required',
            'number_of_subjects' => 'required|numeric',
            'phone_No' => 'required',
            'total_amount' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidateNo  =  $request->candidateNo;
        $centreNo =  $request->centreNo;
        $session = $request->Session;
        $level =  $request->level;
        $amount = $request->total_amount;
        if (!$request->has("increaseSubjects") || is_null($request->increaseSubjects)) {
            CenterCandidate::create([
                "candidate_no" => $candidateNo,
                "session" =>   $session,
                "email" => $request->email_Address,
                "phone_number" => $request->phone_No,
                "sponser" => "P",
                "level" => $level,
                "type" => 3,
                "financial_year" => date('Y') . '-' . (date('Y') + 1),
                "center_no" => $centreNo,
                "subject_number" =>  $request->number_of_subjects,
            ]);
        } else {
            $candidate = CenterCandidate::where('candidate_no', '=', $candidateNo)
                ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1))
                ->first();
            $subject_number = ((int)($candidate->subject_number)) + ((int) $request->number_of_subjects);
            $candidate->subject_number = $subject_number;
            $candidate->save();
        }
        if ($request->has('subject') && !is_null($request->subject)) {
            $subjects =   $subjects = is_array($request->subject) ? explode(",", implode(",", $request->subject)) : explode(',', $request->subject);
            $keys = array_keys($subjects);
            $last = end($keys);
            foreach (range(0, $last - 1, -2) as $key) {
                $subject_code =  $subjects[$key];
                $subject_option = 'A';
                SubjectCandidate::create(
                    [
                        "candidate_no" => $candidateNo,
                        "subject_code" => $subject_code,
                        "level" => $level,
                        "financial_year" => date('Y') . '-' . (date('Y') + 1),
                        "session" => $session,
                        "subject_option" => $subject_option
                    ]
                );
            }
        }
        if ($request->has("mathematics") && !is_null($request->mathematics)) {
            $code = is_array($request->mathematics) ? explode(',', implode('', $request->mathematics))[0] : explode(',', $request->mathematics);
            $option = is_array($request->mathematics) ? explode(" ", explode(',', implode(' ', $request->mathematics))[1]) : explode(' ', $code[1]);
            $subject_code = is_array($request->mathematics) ?  $code :  $code[0];
            $subject_option = '';
            if ($option[1] == "(Core)") {
                $subject_option = "A";
            } else {
                $subject_option = "B";
            }

            SubjectCandidate::create(
                [
                    "candidate_no" => $candidateNo,
                    "subject_code" => $subject_code,
                    "level" => $level,
                    "financial_year" => date('Y') . '-' . (date('Y') + 1),
                    "session" => $session,
                    "subject_option" => $subject_option
                ]
            );
        }
        if ($request->has("physcial_science") && !is_null($request->physcial_science)) {
            $code = is_array($request->physcial_science) ? explode(',', implode('', $request->physcial_science))[0] : explode(',', $request->physcial_science);
            $option = is_array($request->physcial_science) ? explode(" ", explode(',', implode(' ', $request->physcial_science))[1]) : explode(' ', $code[1]);
            $subject_code = is_array($request->physcial_science) ?  $code :  $code[0];
            $subject_option = '';
            if ($option[1] == "(Core)") {
                $subject_option = "A";
            } else {
                $subject_option = "B";
            }
            SubjectCandidate::create(
                [
                    "candidate_no" => $candidateNo,
                    "subject_code" => $subject_code,
                    "level" => $level,
                    "session" => $session,
                    "financial_year" => date('Y') . '-' . (date('Y') + 1),
                    "subject_option" => $subject_option
                ]
            );
        }
        $candidateConfirmation = CandidatePaymentConfirmation::findorFail($id);
        $invoice = new Invoice();
        $invoice->client_id =   $candidateNo;
        $invoice->reference_no =  $candidateConfirmation->bank_ref;
        $invoice->amount = $amount;
        $invoice->level = $level;
        $invoice->financial_year = date('Y') . '-' . (date('Y') + 1);
        $invoice->session = $session;
        $invoice->save();
        $invoiceid = $invoice->id;
        PaymentModel::create([
            "invoice_id" =>  $invoiceid,
            "reference_no" => $candidateConfirmation->bank_ref,
            "amount" => $amount,
        ]);
        return true;
    }


    private function candidateSubjectValidation(Request $request)
    {

        $candidate_number = $request->candidate_no;

        $subjects = [];

        $MATHEMATICS = [];

        $PHYSICAL_SCIENCE = [];

        $all_subjects = array();

        if ($request->has('subject')) {

            $subjects =  $request->subject;

            if (count($subjects) > 0) {

                foreach ($subjects as $subject) {

                    $code = explode(',', $subject);

                    $suject_code = $code[0];

                    $subject_option = 'A';

                    $all_subjects['subjects'][] = array(

                        'candidate_no' => $candidate_number,

                        'subject_code' => (int)$suject_code,

                        'subject_option' =>  $subject_option,

                        'type' => $request->type

                    );
                }
            }
        }

        if ($request->has('mathematics')) {
            $MATHEMATICS = $request->mathematics;
            if (count($MATHEMATICS) >  0) {
                foreach ($MATHEMATICS as $subject) {
                    $code = explode(',', $subject);
                    $option = explode(' ', $code[1]);
                    $suject_code = $code[0];
                    $subject_option = '';
                    if ($option[1] == "(Core)") {

                        $subject_option = "A";
                    } else {

                        $subject_option = "B";
                    }

                    $all_subjects['subjects'][] = array(

                        'candidate_no' => $candidate_number,

                        'subject_code' => (int)$suject_code,

                        'subject_option' =>  $subject_option,

                        'type' => $request->type

                    );
                }
            }
        }

        if ($request->has('physcial_science')) {
            $PHYSICAL_SCIENCE = $request->physcial_science;
            foreach ($PHYSICAL_SCIENCE as $subject) {
                $code = explode(',', $subject);
                $option = explode(' ', $code[1]);
                $suject_code = $code[0];
                $subject_option = '';
                if ($option[1] == "(Core)") {
                    $subject_option = "A";
                } else {
                    $subject_option = "B";
                }
                $all_subjects['subjects'][] = array(
                    'candidate_no' => $candidate_number,
                    'subject_code' => (int)$suject_code,
                    'subject_option' =>  $subject_option,
                    'type' => $request->type

                );
            }
        }

        if (count($all_subjects) == 0) {
            $all_subjects['subjects'][] = array(
                'candidate_no' => "",
                'subject_code' => "",
                'subject_option' =>  "",
                'type' => ""

            );
        }

        return  $all_subjects;
    }
}
