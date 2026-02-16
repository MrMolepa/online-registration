<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\SMS\SmsApi;
use App\Mail\InvitationMail;
use App\Models\CenterCandidate;
use App\Models\Invitation;
use App\Models\InvitationRecipient;
use App\Models\InvitationRecipientField;
use App\Models\InvitationRole;
use App\Models\InvitationScriptFee;
use App\Models\Level;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Workflow;
use App\Services\WorkflowService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Reader;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function PHPSTORM_META\type;

class InvitationController extends Controller
{
    public function index(Request $request)
    {




        $recipients = InvitationRecipient::with([
            'recipientFields',
            'invitations' => function ($q) {
                $q->orderBy('sent_at', 'desc'); // Order invitations
            },
            'invitations.role'
        ]);


        $type = DB::select(DB::raw("SHOW COLUMNS FROM `invitation_roles` WHERE Field = 'type'"))[0]->Type;

        $types = explode(',', str_replace(["enum(", ")", "'"], '', $type));

        if ($request->ajax()) {

            // Filter by invitations columns and role
            $recipients->whereHas('invitations', function ($q) use ($request) {
                if ($request->filled('financial_year')) {
                    $q->where('financial_year', $request->financial_year);
                }
                if ($request->filled('level')) {
                    $q->where('level', $request->level);
                }
                if ($request->filled('session')) {
                    $q->where('session', $request->input('session'));
                }
                if ($request->filled('role')) {
                    $q->where('role_id', $request->role);
                }
            });
            $recipients =   $recipients->get();
            if ($request->has('analysis')) {
                $totalRecipients = $recipients->count();
                $totalInvitations = $recipients->flatMap->invitations->count();
                $completedInvitations = $recipients->flatMap->invitations->where('status', 'complete')->count();
                $sentendInvitations = $recipients->flatMap->invitations->where('status', 'sent')->count();
                $byRole = $recipients->flatMap->invitations
                    ->groupBy(fn($inv) => $inv->role->name ?? 'Unknown')
                    ->map->count();

                $monthlyInvitations = $recipients->flatMap->invitations
                    ->groupBy(fn($inv) => Carbon::parse($inv->sent_at)->format('Y-m'))
                    ->map->count();
                return response()->json([
                    'sentInvitations' => $sentendInvitations,
                    'totalRecipients' => $totalRecipients,
                    'totalInvitations' => $totalInvitations,
                    'completedInvitations' => $completedInvitations,
                    'byRole' => $byRole,
                    'monthlyInvitations' => $monthlyInvitations,
                ]);
            }

            if ($request->has('center_filter')) {
                $centerQuery = DB::table('centers')->select(['center_no', 'center_name']);

                if ($request->has('level')) {
                    $centerQuery->where('status', '=', '0')
                        ->where('level', $request->level);
                }
                $centers = $centerQuery->pluck('center_name', 'center_no',)->toArray();
                return response()->json([
                    'centers' => $centers,
                ]);
            }


            return datatables()->of($recipients)
                ->addColumn('action', function ($recipient) {
                    $edit = '<a href="' . route('admin.invitations.recipients.edit', $recipient->id) . '" class="btn btn-xs btn-primary edit-recipient">Edit</a>';
                    $delete = '<a href="' . route('admin.invitations.recipients.destroy', $recipient->id) . '" class="btn btn-xs btn-danger delete-recipient">Delete</a>';
                    return $edit . ' ' . $delete;
                })
                ->addColumn('checkbox', function ($recipient) {
                    return "<input type='checkbox' class='recipient-checkbox' name='recipients[]' value='$recipient->id'>";
                })
                ->addColumn('invitations', function ($recipient) {
                    // Output each invitation’s action buttons
                    return $recipient->invitations->map(function ($invitation) {
                        $resend = '<a href="' . route('admin.invitations.resend', $invitation->id) . '"  class="btn btn-sm btn-warning resend-invitation">Send</a>';
                        $edit = '<a href="' . route('admin.invitations.edit', $invitation->id) . '" class="btn btn-xs btn-primary edit-invitation">Edit</a>';
                        $delete = '<a href="' . route('admin.invitations.destroy', $invitation->id) . '" class="btn btn-xs btn-danger delete-invitation">Delete</a>';

                        $action = "$resend $edit $delete";
                        return [
                            'id' => $invitation->id,
                            'center_no' => $invitation->center_no,
                            'session' => $invitation->session,
                            'financial_year' => $invitation->financial_year,
                            'role' => $invitation->role->name,
                            'status' => $invitation->status,
                            'sent_at' => $invitation->sent_at,
                            'responded_at' => $invitation->responded_at,
                            'action' =>  $action,
                        ];
                    });
                })
                ->rawColumns(['action', 'invitations', 'checkbox'])
                ->toJson();
        }

        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
        $sessions = Session::where('financial_year', $years[0])->get();
        $levels = Level::get();
        $roles = InvitationRole::get();
        $subjects = Subject::get();
        $customSourceTypes = ['subjects', 'centers'];


        $workflows = Workflow::get();
        return view('admin.invitations.index', compact('roles', 'workflows', 'years', 'levels', 'sessions', 'customSourceTypes', 'subjects', 'types'));
    }


    public function store(Request $request)
    {
        // 1️⃣ Validate recipient basic info
        $basicRules = [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone_number' => 'required|numeric|regex:/^[0-9]{8}$/',
            'national_id'     =>  ['nullable', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
            'level'     => 'required|string|max:50',
            'email'           => 'required|email',
            'role_id'         => 'required|exists:invitation_roles,id',
            'session'         => 'required|string|max:50',
            'center_no'         => 'required|string|max:50',
            'workflow'         => 'required|exists:workflows,id',
            'financial_year'  => 'required|string|max:50',
            'start_date'     => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        $validator = Validator::make($request->all(), $basicRules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $role = InvitationRole::with(['fields' => function ($query) {
            $query->where('source_type', 'custom');
        }])->findOrFail($request->role_id);

        // 2️⃣ Find or create recipient
        $recipient = InvitationRecipient::updateOrCreate(
            [
                'email' => $request->email,
            ],
            [
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'center_no'   => $request->center_no,
                'phone_number'   => $request->phone_number,
                'national_id' => $request->national_id,
                'type' => $role->type,
            ]
        );

        // 3️⃣ Create or update invitation for this recipient
        $invitation = Invitation::updateOrCreate(
            [
                'recipient_id' => $recipient->id,
                'role_id' => $request->role_id,
                'center_no'   => $request->center_no,
                'session' => $request->input('session'),
                'level' => $request->input('level'),
                'financial_year' => $request->input('financial_year'),
            ],
            [
                'status'         => 'pending',
                'response_token' =>  Str::uuid(),
                'responded_at'   =>  null,
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
            ]
        );

        // 4️⃣ Save dynamic fields
        if ($request->has('fields') && is_array($request->fields)) {
            foreach ($role->fields as $field) {
                $fieldValue = $request->fields[$field->id] ?? null;
                if (is_array($fieldValue)) $fieldValue = implode(',', $fieldValue);
                InvitationRecipientField::updateOrCreate(
                    [
                        'invitation_id' => $invitation->id,
                        'recipient_id' => $recipient->id,
                        'field_id'     => $field->id,
                    ],
                    [
                        'field_key'   => $field->name,
                        'field_value' => $fieldValue,
                    ]
                );
            }
        }
        // start workflow
        $workflowService = new WorkflowService();
        $workflowService->startWorkflow($request->workflow, get_class($invitation), $invitation->id, auth()->user()->id);


        return response()->json([
            'success' => 'Recipient invitation saved successfully!',
        ]);
    }




    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:51200', // allow up to ~50MB
        ]);

        $file = $request->file('csv_file')->getRealPath();

        $handle = fopen($file, 'r');
        if ($handle === false) {
            return response()->json(['errors' => ['Could not open uploaded file']], 422);
        }
        $errors = [];
        $rowNumber = 0;
        $header = null;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rowNumber++;

            // First row → treat as header
            if ($rowNumber === 1) {
                $header = array_map('trim', $row);
                continue;
            }

            // Map row to associative array with headers
            $rowData = array_combine($header, $row);
            if ($rowData === false) {
                $errors[$rowNumber][] = 'Invalid column count at row ' . $rowNumber;
                continue;
            }

            try {
                DB::transaction(function () use ($rowData, $rowNumber, &$errors) {
                    $validator = Validator::make($rowData, [
                        'national_id'     => ['nullable', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                        'first_name'      => 'required|string|max:255',
                        'last_name'       => 'required|string|max:255',
                        'phone_number' => 'required|numeric|regex:/^[0-9]{8}$/',
                        'email'           => 'required|email',
                        'role_id'         => 'required|exists:invitation_roles,id',
                        'level'           => 'required|string|max:50',
                        'center_no'           => 'required|string|max:50',
                        'session'         => 'required|string|max:50',
                        'financial_year'  => 'required|string|max:50',
                        'workflow'         => 'required|exists:workflows,id',
                        'start_date' => 'nullable|date',
                        'end_date'   => 'nullable|date|after_or_equal:start_date',
                    ]);

                    if ($validator->fails()) {
                        $errors[$rowNumber] = $validator->errors()->all();
                        return;
                    }

                    // ✅ Upsert recipient
                    $recipient = InvitationRecipient::updateOrCreate(
                        [
                            'email' => $rowData['email'],

                        ],
                        [
                            'center_no' => $rowData['center_no'],
                            'first_name'   => $rowData['first_name'],
                            'last_name'    => $rowData['last_name'],
                            'phone_number' => $rowData['phone_number'],
                            'national_id'  => $rowData['national_id'],
                        ]
                    );

                    // ✅ Upsert invitation
                    $invitation = Invitation::updateOrCreate(
                        [
                            'recipient_id'   => $recipient->id,
                            'role_id'        => $rowData['role_id'],
                            'center_no' => $rowData['center_no'],
                            'session'        => $rowData['session'] ?? null,
                            'level'          => $rowData['level'],
                            'financial_year' => $rowData['financial_year'],
                        ],
                        [
                            'status'         => 'pending',
                            'response_token' => Str::uuid(),
                            'responded_at'   => null,
                            'start_date' => $rowData['start_date'] ?: null,
                            'end_date'   => $rowData['end_date'] ?: null,
                        ]
                    );

                    // start workflow
                    $workflowService = new WorkflowService();
                    $workflowService->startWorkflow($rowData['workflow'], get_class($invitation), $invitation->id, auth()->user()->id);

                    // ✅ Handle dynamic fields if present
                    $role = InvitationRole::with(['fields' => function ($q) {
                        $q->where('source_type', 'custom');
                    }])->findOrFail($rowData['role_id']);

                    foreach ($role->fields as $field) {
                        if (!array_key_exists($field->name, $rowData)) continue;

                        $fieldValue = $rowData[$field->name] ?? null;
                        if (is_array($fieldValue)) $fieldValue = implode(',', $fieldValue);

                        InvitationRecipientField::updateOrCreate(
                            [
                                'invitation_id' => $invitation->id,
                                'recipient_id'  => $recipient->id,
                                'field_id'      => $field->id,
                            ],
                            [
                                'field_key'     => $field->name,
                                'field_value'   => $fieldValue,
                            ]
                        );
                    }
                });
            } catch (Exception $e) {
                $errors[$rowNumber][] = $e->getMessage();
            }
        }

        fclose($handle);

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        return response()->json(['success' => 'CSV import completed successfully!']);
    }


    public function exportCsv(Request $request)
    {
        // Generate unique filename
        $filename = 'invitations_export_' . date('Ymd_His') . '.csv';
        $filePath = storage_path('app/public/exports/' . $filename);

        // Ensure export folder exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        // Open file for writing
        $output = fopen($filePath, 'w');

        $headerWritten = false;

        // Build base query with optional filters
        $query = Invitation::with(['role.fields', 'payment', 'recipient', 'invitationFields']);

        if ($request->filled('financial_year')) {
            $query->where('financial_year', $request->financial_year);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('session')) {
            $query->where('session', $request->input('session'));
        }
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $query->chunk(500, function ($invitations) use (&$headerWritten, $output) {
            foreach ($invitations as $invitation) {
                // Write header once
                if (!$headerWritten) {
                    $header = [
                        'Role',
                    ];

                    foreach ($invitation->role->fields as $field) {
                        $header[] = ucwords(str_replace('_', ' ', $field->name));
                    }

                    fputcsv($output, $header);
                    $headerWritten = true;
                }

                $row = [
                    $invitation->role->name ?? '',
                ];

                foreach ($invitation->role->fields as $field) {
                    switch ($field->source_type) {
                        case 'recipient':
                            $value = $invitation->recipient->{$field->name} ?? '';
                            break;
                        case 'payment':
                            $value = $invitation->payment->{$field->name} ?? '';
                            break;
                        case 'script_fee':
                            $invitation_s = $invitation->invitationFields
                                ->whereIn('field_key', ['subject_code', 'component_no'])
                                ->pluck('field_value', 'field_key')
                                ->toArray();
                            $component_code = str_pad($invitation_s['subject_code'] ?? '', 4, '0', STR_PAD_LEFT) .
                                str_pad($invitation_s['component_no'] ?? '', 2, '0', STR_PAD_LEFT);
                            $value = InvitationScriptFee::where('component_code', $component_code)
                                ->value($field->key_column) ?? 'No fee';
                            break;
                        default:
                            $response = $invitation->invitationFields
                                ->where('field_id', $field->id)
                                ->first();
                            $value = $response ? $response->field_value : '';
                    }
                    $row[] = $value;
                }

                fputcsv($output, $row);
            }
        });

        fclose($output);

        // Return JSON with download URL
        return response()->json([
            'success' => true,
            'url' => asset('storage/exports/' . $filename)
        ]);
    }






    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:invitation_roles,id',
        ]);

        $role = InvitationRole::with(['fields' => function ($q) {
            $q->where('source_type', 'custom');
        }])->findOrFail($request->role_id);

        // ✅ Base columns
        $columns = [
            'national_id',
            'first_name',
            'last_name',
            'phone_number',
            'email',
            'role_id',
            'level',
            'session',
            'financial_year',
            'center_no',
            'workflow',
            'start_date',
            'end_date'
        ];

        // ✅ Add custom fields
        foreach ($role->fields as $field) {
            $columns[] = $field->name;
        }

        // ✅ Generate CSV stream
        $response = new StreamedResponse(function () use ($columns, $role) {
            $handle = fopen('php://output', 'w');

            // Write header
            fputcsv($handle, $columns);

            // Write one sample row
            fputcsv($handle, [
                '1234567890',
                'John',
                'Doe',
                '59000000',
                'john.doe@email.com',
                $role->id,
                'LGCSE',
                'November',
                '2025-2026',
                'LGA500',
                '1',
                '2025-12-01',
                '2025-12-31',
                // Custom fields blank
                ...array_fill(0, count($role->fields), '')
            ]);

            fclose($handle);
        });
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="invitation_template_role_' . $role->id . '.csv"');

        return $response;
    }



    public function resend(Invitation $invitation)
    {
        $token = Str::uuid();
        //Optional: regenerate token if you want
        $invitation->response_token =  $token;
        $invitation->status = 'sent';
        $invitation->sent_at = now();
        $invitation->save();
        $recipient = InvitationRecipient::find($invitation->recipient_id);
        $url = route('applications.invitation.response', $token);
        Mail::to($recipient->email)->send(new InvitationMail($recipient, $invitation, $url));

        $msisdn =  $recipient->phone_number;
        $message = "Dear {$recipient->first_name} {$recipient->last_name},\n"
            . "You are invited to serve as a {$invitation->role->name} for the "
            . "$invitation->session  $invitation->financial_year upcoming examination session..\n"
            . "Accept offer: $url";
        SmsApi::message($msisdn, $message);
        return response()->json([
            'success' => 'Invitation resent successfully',
        ]);
    }


    public function bulkResend(Request $request)
    {


        $request->validate([
            'level' => 'required',
            'session' => 'required',
            'financial_year' => 'required',
            'recipients' => 'required|array',
            'recipients.*' => 'required|integer|exists:invitation_recipients,id',
        ]);



        // Fetch invitations for these recipients with recipient eager-loaded
        $invitations = Invitation::with('recipient')
            ->whereIn('recipient_id', $request->recipients)
            ->where('level', $request->level)
            ->where('financial_year', $request->financial_year)
            ->where('session', $request->input('session'))
            ->latest('sent_at')
            ->get()
            ->unique('recipient_id'); // ensure only 1 per recipient (latest)




        // Pre-generate new tokens for all invitations
        $updates = [];
        foreach ($invitations as $invitation) {
            $updates[$invitation->id] = [
                'response_token' => (string) Str::uuid(),
                'status' => 'sent',
                'sent_at' => now(),
            ];
        }

        // Bulk update using query builder
        foreach ($updates as $id => $data) {
            Invitation::where('id', $id)->update($data);
        }



        // Queue email sending (instead of sending synchronously)
        foreach ($invitations as $invitation) {
            $url = route('applications.invitation.response', $updates[$invitation->id]['response_token']);
            // Dispatch to queue for better performance
            Mail::to($invitation->recipient->email)
                ->queue(new InvitationMail($invitation->recipient, $invitation, $url));

            $msisdn = $invitation->recipient->phone_number;
            $message = "Dear {$invitation->recipient->first_name} {$invitation->recipient->last_name},\n"
                . "You are invited to serve as a {$invitation->role->name} for the "
                . "$invitation->session $invitation->financial_year upcoming examination session..\n"
                . "Accept offer: $url";

            SmsApi::message($msisdn, $message);
        }

        return response()->json([
            'success' => $invitations->count() . ' invitations queued for resend',
        ]);
    }





    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Load invitation with its dynamic fields
        $invitation = Invitation::with(['invitationFields' => function ($q) {
            $q->select('id', 'invitation_id', 'field_id', 'field_value');
        }])->findOrFail($id);

        $url = route('admin.invitations.update', $id);
        return response()->json(['invitation' => $invitation, 'url' => $url]);
    }



    public function update(Request $request, Invitation $invitation)
    {
        // 1️⃣ Validate recipient basic info
        $validationRules = [
            'role_id'        => 'required|exists:invitation_roles,id',
            'session'        => 'required|string|max:50',
            'level'        => 'required|string|max:50',
            'center_no'  => 'required|string|max:50',
            'financial_year' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ];

        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        // 2️⃣ Update existing invitation
        $invitation->update([
            'role_id'        => $request->role_id,
            'session'        => $request->input('session'),
            'financial_year' => $request->financial_year,
            'level' => $request->level,
            'center_no'  => $request->center_no,
            'responded_at'   => null,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        // 3️⃣ Save dynamic fields
        $role = InvitationRole::with(['fields' => function ($query) {
            $query->where('source_type', 'custom');
        }])->findOrFail($request->role_id);

        if ($request->has('fields') && is_array($request->fields)) {
            foreach ($role->fields as $field) {
                $fieldValue = $request->fields[$field->id] ?? null;
                if (is_array($fieldValue)) {
                    $fieldValue = implode(',', $fieldValue);
                }
                InvitationRecipientField::updateOrCreate(
                    [
                        'invitation_id' => $invitation->id,
                        'recipient_id'  => $invitation->recipient_id,
                        'field_id'      => $field->id,
                    ],
                    [
                        'field_key'   => $field->name,
                        'field_value' => $fieldValue,
                    ]
                );
            }
        }

        return response()->json([
            'success'    => 'Recipient invitation updated successfully!',
        ]);
    }


    public function destroy($id)
    {
        $invitation = Invitation::with('payment', 'invitationFields')->find($id);

        if (!$invitation) {
            return response()->json(['error' => 'Invitation not found.'], 404);
        }

        // Prevent deletion if payment exists
        if ($invitation->payment()->exists()) {
            return response()->json(['error' => 'Cannot delete invitation with payment.']);
        }

        // Delete all invitation fields
        $invitation->invitationFields->each->delete();

        // Delete invitation
        $invitation->delete();

        return response()->json(['success' => 'Invitation deleted successfully!']);
    }
}
