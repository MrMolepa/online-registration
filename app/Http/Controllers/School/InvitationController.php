<?php

namespace App\Http\Controllers\School;

use App\Libraries\Payment\Payment;
use App\Mail\InvigilatorMail;
use App\Models\Center;
use App\Models\InvigilationStatus;
use App\Models\InvigilationType;
use App\Models\InvigilatorExperience;
use App\Models\InvigilatorProfile;
use App\Models\Invitation;
use App\Models\InvitationRecipient;
use App\Models\Session;
use App\Models\WorkflowInstance;
use App\Notifications\InvigilatorNotification;
use App\Services\WorkflowService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isNull;

class InvitationController
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function index(Request $request, $type)
    {


        $center = Center::with('subjects')->where('center_no', '=', auth()->user()->center_no)->first();
        $centerSessions = json_decode($center->sessions, true);

        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();


        $invitations = Invitation::where('invitations.session', $session->session)
            ->where('invitations.financial_year', $session->financial_year)
            ->where('invitations.center_no', $center->center_no)
            ->with([
                'recipient.center.principal',  // eager load principals via recipient’s center
                'role.fields',
                'workflowInstance'
            ]);



        //

        if ($request->ajax()) {
            return DataTables::of($invitations)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($invitation) {
                    if (!is_null($invitation->workflowInstance) && $invitation->status=='complete' || $invitation->workflowInstance->status !='completed') {
                        $instance_id = $invitation->workflowInstance->id;
                        return "<input type='checkbox' class='invitation-checkbox' name='invitations[]' value='$instance_id'>";
                    }
                    return '';
                })

                ->addColumn('status', function ($invitation) {
                    if (!is_null($invitation->workflowInstance)) {
                        return  $invitation->workflowInstance->status;
                    }
                    return '';
                })->rawColumns(['checkbox', 'status'])
                ->make(true);
        }

        $principal = $invitations->first()?->recipient?->center?->principal;

        return view("school.invitations.$type", compact('principal'));
    }


    public function process(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'invitations' => 'required|array',
                'invitations.*' => 'required|integer|exists:workflow_instances,id',
                'first_name'      => 'required|string|max:255',
                'last_name'       => 'required|string|max:255',
                'phone_number' => 'required|numeric|regex:/^[0-9]{8}$/',
                'national_id'     =>  ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                'declaration'     => 'required',
                'email'           => 'required|email',
                'action' => 'required|in:approve,reject',
                'comments' => 'nullable|string|max:1000',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $instances = WorkflowInstance::whereIn('id', $request->invitations)->get();

        foreach ($instances as  $instance) {
            $stepInstance = $this->workflowService->processStep(
                $instance->id,
                $request->action,
                $request->comments ?? null
            );
            if (!$stepInstance) {
                return response()->json(['error' => 'Unauthorized action: user not assigned to any pending step.'], 422);
            }

        }

        $type = 'principal';
        // Update existing record or create new one
        InvitationRecipient::updateOrCreate(
            [
                'email' => $request->email, // find by email
                'type'  => $type,
                'center_no'   =>  auth()->user()->center_no,
            ],
            [
                'first_name'   => $request->first_name,
                'national_id'   => $request->national_id,
                'last_name'    => $request->last_name,
                'phone_number' => $request->phone_number,
            ]
        );

        return response()->json(['success' => "Workflow step {$request->action}d successfully."]);
    }
}
