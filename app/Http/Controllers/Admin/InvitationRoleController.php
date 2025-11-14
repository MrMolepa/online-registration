<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationFieldPosition;
use App\Models\InvitationRecipientField;
use App\Models\InvitationRole;
use App\Models\InvitationRoleField;
use App\Models\Level;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InvitationRoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = InvitationRole::withCount('fields')->get();

            //designer
            return datatables()->of($roles)
                ->addColumn('designer', function ($role) {
                    $designer = '<a href="' . route('admin.invitations.roles.designer', $role->id) . '" class="btn  btn-xs btn-default">Designer</a>';
                    return $designer;
                })
                ->addColumn('action', function ($role) {
                    $copy = '<a href="' . route('admin.invitations.roles.copyPositions', $role->id) . '" data-id="' . $role->id . '" class="btn copy-position btn-xs btn-info">Copy</a>';
                    $edit = '<a href="' . route('admin.invitations.roles.edit', $role->id) . '" class="btn edit-role btn-xs btn-primary">Edit</a>';
                    $delete = '<a href="' . route('admin.invitations.roles.destroy', $role->id) . '" class="btn btn-xs btn-danger delete-role">Delete</a>';
                    return  $copy . ' ' . $edit . ' ' . $delete;
                })
                ->rawColumns(['action', 'designer'])
                ->make(true);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Role basics
            'name'        => 'required|string|max:255|unique:invitation_roles,name',
            'description' => 'required|string|max:255',
            'type' => 'required|string|max:255',

            // Contract template file
            'contract_template' => 'nullable|file|mimes:pdf|max:2048',

            // Fields
            'fields'              => 'required|array|min:1',
            'fields.*.label'      => 'required|string|max:255',
            'fields.*.name'       => 'required|string|max:255|alpha_dash',
            'fields.*.type'       => 'required|in:text,number,date,select,checkbox,radio,file',
            'fields.*.required'   => 'nullable|boolean',

            // Source + options
            'fields.*.source'      => 'nullable|string|max:255|in:manual,subjects,components,centers,levels,syllabi',
            'fields.*.key_column'   => 'nullable|string|max:255',
            'fields.*.value_column' => 'nullable|string|max:255',
            'fields.*.options'     => 'nullable|array',
            'fields.*.options.*'   => 'nullable|string|max:255',
        ]);

        // Conditional validation for options (safe checks)
        $validator->sometimes('fields.*.options', 'required|array|min:1', function ($input) {
            if (empty($input->fields)) return false;
            foreach ($input->fields as $field) {
                if (
                    isset($field['type'], $field['source']) &&
                    in_array($field['type'], ['select', 'checkbox', 'radio']) &&
                    $field['source'] === 'manual'
                ) {
                    return true;
                }
            }
            return false;
        });

        $validator->sometimes('fields.*.options.*', 'required|string|max:255', function ($input) {
            if (empty($input->fields)) return false;

            foreach ($input->fields as $field) {
                if (
                    isset($field['type'], $field['source']) &&
                    in_array($field['type'], ['select', 'checkbox', 'radio']) &&
                    $field['source'] === 'manual'
                ) {
                    return true;
                }
            }
            return false;
        });


        // Handle errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Handle PDF upload
        $role = new InvitationRole();
        $role->name = $request->name;
        $role->type = $request->type;
        $role->description = $request->description;
        if ($request->hasFile('contract_template')) {
            $file = $request->file('contract_template');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('contract_templates', $filename); // stored in storage/app/roles
            // Delete old file if exists
            $role->contract_template = $path;
        }
        // Create Role
        $role->save();

        $recipientColumns = Schema::getColumnListing('invitation_recipients');
        // Optional: exclude id, timestamps, or any columns you don’t want
        $recipientColumns = array_diff($recipientColumns, ['id', 'created_at', 'updated_at']);



        $centerColumns = Schema::getColumnListing('invitation_recipients');
        // Optional: exclude id, timestamps, or any columns you don’t want
        $centerColumns = array_intersect($centerColumns, ['center_no', 'ceneter_no']);





        $scriptFeeColumns = Schema::getColumnListing('invitation_script_fee');
        // Optional: exclude id, timestamps, or any columns you don’t want
        $scriptFeeColumns = array_diff($scriptFeeColumns, ['subject_code', 'component_no', 'session', 'financial_year', 'component_code', 'created_at', 'updated_at']);

        $paymentMethodColumns = Schema::getColumnListing('invitation_payments');
        // Optional: exclude id, timestamps, or any columns you don’t want
        $paymentMethodColumns = array_diff($paymentMethodColumns, ['id', 'payment_id', 'invitation_id', 'created_at', 'updated_at']);

        // Build array with label = column name for admin
        //recipient
        $additionalFields = [];
        foreach ($recipientColumns as $col) {
            $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
            $additionalFields[] = [
                'name' => $col,
                'label' => $label,
                'source_type' => 'recipient'
            ];
        }
        // Build array with label = column name for admin
        foreach ($centerColumns as $col) {
            $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
            $additionalFields[] = [
                'name' => $col,
                'label' => $label,
                'source_type' => 'center',
                'key_column' => 'center_no'
            ];
        }


        foreach ($scriptFeeColumns as $col) {
            $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
            $additionalFields[] = [
                'name' => $col,
                'label' => $label,
                'source_type' => 'script_fee',
                'key_column' => 'script_fee'
            ];
        }
        // Build array with label = column name for admin
        foreach ($paymentMethodColumns as $col) {
            $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
            $additionalFields[] = [
                'name' => $col,
                'label' => $label,
                'source_type' => 'payment'
            ];
        }



        // Save Fields
        foreach ($request->fields as $field) {
            $options = null;

            if (in_array($field['type'], ['select', 'checkbox', 'radio'])) {
                if (($field['source'] ?? 'manual') === 'manual') {
                    $options = isset($field['options']) ? json_encode($field['options']) : null;
                } else {
                    // DB source
                    $sourceTable = $field['source'];
                    if (Schema::hasTable($sourceTable)) {
                        $keyColumn   = $field['key_column'] ?? 'id';
                        $valueColumn = $field['value_column'] ?? 'name';

                        $dbOptions = DB::table($sourceTable)
                            ->pluck($valueColumn, $keyColumn)
                            ->toArray();

                        $options = json_encode($dbOptions);
                    }
                }
            }

            $role->fields()->create([
                'label'       => $field['label'],
                'name'        => $field['name'],
                'type'        => $field['type'],
                'source'      => $field['type'] === 'select' ? ($field['source'] ?? 'manual') : null,
                'key_column'  => $field['key_column'] ?? null,
                'value_column' => $field['value_column'] ?? null,
                'required'    => isset($field['required']) ? 1 : 0,
                'options'     => $options,
            ]);
        }

        foreach ($additionalFields as $field) {
            InvitationRoleField::updateOrCreate(
                [
                    'role_id' => $role->id,
                    'name' => $field['name'],
                    'source_type' => $field['source_type'],

                ],
                [
                    'label' => $field['label'],
                    'type' => 'text',
                    'required' => true
                ]
            );
        }



        return response()->json(['success' => 'Role created successfully!']);
    }


    public function pdfTemplate(InvitationRole $role)
    {
        $path = $role->contract_template;

        if (!Storage::exists($path)) {
            abort(404);
        }
        return response()->file(storage_path('app/' . $path), [
            'Content-Type' => 'application/pdf'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = InvitationRole::with(['fields.positions', 'fields' => function ($query) {
            $query->where('source_type', 'custom');
        }])->find($id);

        $url = route('admin.invitations.roles.update', $id);
        return response()->json(['role' => $role, 'url' => $url]);
    }


    public function designer(string $id)
    {
        $role = InvitationRole::with('fields.positions')->find($id);
        $pdfUrl = route('admin.invitations.roles.pdfTemplate', $role->id);

        return view('admin.invitations.designer', compact('role', 'pdfUrl'));
    }


    /**
     * Store a new position (copy field to another page).
     */
    public function copyField(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:invitation_role_fields,id',
            'page'     => 'required|integer|min:1',
            'pos_x'    => 'required|numeric',
            'pos_y'    => 'required|numeric',
            'width'    => 'nullable|numeric',
            'height'   => 'nullable|numeric',
        ]);

        // Find the original field
        $field = InvitationRoleField::findOrFail($validated['field_id']);
        // Duplicate field with new page + position
        $copy = $field->replicate(); // clone the row
        $copy->page   = $validated['page'];
        $copy->pos_x  = $validated['pos_x'];
        $copy->pos_y  = $validated['pos_y'];
        $copy->width  = $validated['width'] ?? $field->width;
        $copy->height = $validated['height'] ?? $field->height;
        $copy->save();
        return response()->json([
            'status'  => 'success',
            'message' => 'Field copied successfully',
            'field'   => $copy,
        ]);
    }






    public function saveField(Request $request)
    {

        $validated = $request->validate([
            'field_id'   => 'required|exists:invitation_role_fields,id',
            'page'       => 'required|integer|min:1',
            'pos_x'      => 'required|numeric',
            'pos_y'      => 'required|numeric',
            'width'      => 'nullable|numeric',
            'height'     => 'nullable|numeric',
            'is_visible' => 'nullable',
            'position_id' => 'nullable|exists:invitation_field_positions,id', // optional for existing
        ]);


        if (isset($validated['position_id'])) {
            // Update existing position
            $position = InvitationFieldPosition::findOrFail($validated['position_id']);

            if ($request->has('is_visible')) {
                $field = InvitationRoleField::findOrFail($request->field_id);
                $field->is_visible = filter_var($request->is_visible, FILTER_VALIDATE_BOOLEAN);
                $field->save();
            }

            if ($request->has('pos_x')) {
                $position->pos_x = $request->pos_x;
            }

            if ($request->has('pos_y')) {
                $position->pos_y = $request->pos_y;
            }

            if ($request->filled('width')) {   // only update if provided
                $position->width = $request->width;
            }

            if ($request->filled('height')) {  // only update if provided
                $position->height = $request->height;
            }

            if ($request->has('page')) {
                $position->page = $request->page;
            }
            $position->save();
            return response()->json(['success' => true, 'position' => $position]);
        } else {
            if ($request->has('is_visible')) {
                $field = InvitationRoleField::findOrFail($request->field_id);
                $field->is_visible = filter_var($request->is_visible, FILTER_VALIDATE_BOOLEAN);
                $field->save();
            }
            // Create new position (even if field exists on same page)
            $position = InvitationFieldPosition::create([
                'field_id'   => $validated['field_id'],
                'page'       => $validated['page'],
                'pos_x'      => $validated['pos_x'],
                'pos_y'      => $validated['pos_y'],
                'width'      => $validated['width'] ?? 120,
                'height'     => $validated['height'] ?? 50,
                'is_visible' => $validated['is_visible'] ?? 1,
            ]);

            return response()->json(['success' => true, 'position' => $position]);
        }
    }


    public function removeField(Request $request)
    {
        $position = InvitationFieldPosition::findOrFail($request->position_id);
        $position->delete();
        return response()->json(['success' => 'Role deleted successfully.']);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // Role basics
            'name' => 'required|string|max:255|unique:invitation_roles,name,' . $id,
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            // Contract template file
            'contract_template' => 'nullable|file|mimes:pdf|max:2048',
            // Dynamic fields
            'fields'                  => 'required|array|min:1',
            'fields.*.id'             => 'nullable|integer|exists:invitation_role_fields,id',
            'fields.*.label'          => 'required|string|max:255',
            'fields.*.name'           => 'required|string|max:255|alpha_dash',
            'fields.*.type'           => 'required|in:text,number,date,select,checkbox,radio,file',
            'fields.*.required'       => 'nullable|boolean',
            // Positions
            'fields.*.positions'      => 'nullable|array',
            'fields.*.positions.*.id' => 'nullable|integer|exists:invitation_field_positions,id',
            // Source + options
            'fields.*.source'         => 'nullable|string|max:255|in:manual,subjects,centers,components,levels,syllabi',
            'fields.*.key_column'     => 'nullable|string|max:255',
            'fields.*.value_column'   => 'nullable|string|max:255',
            'fields.*.options'        => 'nullable|array',
            'fields.*.options.*'      => 'nullable|string|max:255',
        ]);

        // Conditional validation for options (safe checks)
        $validator->sometimes('fields.*.options', 'required|array|min:1', function ($input) {
            if (empty($input->fields)) return false;
            foreach ($input->fields as $field) {
                if (
                    isset($field['type'], $field['source']) &&
                    in_array($field['type'], ['select', 'checkbox', 'radio']) &&
                    $field['source'] === 'manual'
                ) {
                    return true;
                }
            }
            return false;
        });

        $validator->sometimes('fields.*.options.*', 'required|string|max:255', function ($input) {
            if (empty($input->fields)) return false;

            foreach ($input->fields as $field) {
                if (
                    isset($field['type'], $field['source']) &&
                    in_array($field['type'], ['select', 'checkbox', 'radio']) &&
                    $field['source'] === 'manual'
                ) {
                    return true;
                }
            }
            return false;
        });



        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $role = InvitationRole::findOrFail($id);
            $role->name = $request->name;
            $role->type = $request->type;
            $role->description = $request->description ?? null;

            // Handle contract template upload
            if ($request->hasFile('contract_template')) {
                $file = $request->file('contract_template');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('contract_templates', $filename); // stored in storage/app/roles
                // Delete old file if exists
                if ($role->contract_template && Storage::disk('local')->exists($role->contract_template)) {
                    Storage::disk('local')->delete($role->contract_template);
                }
                $role->contract_template = $path;
            }

            $role->save();


            $recipientColumns = Schema::getColumnListing('invitation_recipients');
            // Optional: exclude id, timestamps, or any columns you don’t want
            $recipientColumns = array_diff($recipientColumns, ['id', 'created_at', 'updated_at']);


            $centerColumns = Schema::getColumnListing('invitation_recipients');
            // Optional: exclude id, timestamps, or any columns you don’t want
            $centerColumns = array_intersect($centerColumns, ['center_no', 'center_name']);

            $scriptFeeColumns = Schema::getColumnListing('invitation_script_fee');
            // Optional: exclude id, timestamps, or any columns you don’t want
            $scriptFeeColumns = array_diff($scriptFeeColumns, ['subject_code', 'component_no', 'session', 'financial_year', 'component_code', 'created_at', 'updated_at']);

            $paymentMethodColumns = Schema::getColumnListing('invitation_payments');
            // Optional: exclude id, timestamps, or any columns you don’t want
            $paymentMethodColumns = array_diff($paymentMethodColumns, ['id', 'payment_id', 'invitation_id', 'created_at', 'updated_at']);

            // Build array with label = column name for admin
            //recipient
            $additionalFields = [];
            foreach ($recipientColumns as $col) {
                $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
                $additionalFields[] = [
                    'name' => $col,
                    'label' => $label,
                    'source_type' => 'recipient'
                ];
            }


            foreach ($centerColumns as $col) {
                $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
                $additionalFields[] = [
                    'name' => $col,
                    'label' => $label,
                    'source_type' => 'center',
                    'key_column' => 'center_no'
                ];
            }
            // Build array with label = column name for admin
            foreach ($scriptFeeColumns as $col) {
                $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
                $additionalFields[] = [
                    'name' => $col,
                    'label' => $label,
                    'source_type' => 'script_fee',
                    'key_column' => 'script_fee'
                ];
            }
            // Build array with label = column name for admin
            foreach ($paymentMethodColumns as $col) {
                $label = ucwords(str_replace('_', ' ', $col)); // convert snake_case to readable
                $additionalFields[] = [
                    'name' => $col,
                    'label' => $label,
                    'source_type' => 'payment'
                ];
            }


            /**
             * Step 1: Handle incoming fields
             */

            $incomingFields = collect($request->input('fields', []));
            $incomingFieldIds = $incomingFields->pluck('id')->filter()->all();

            // Delete only manual fields missing in request
            $fieldsToDelete = $role->fields()
                ->whereNotIn('id', $incomingFieldIds)
                ->where(function ($q) {
                    $q->whereNull('source_type')
                        ->orWhere('source_type', 'manual');
                })
                ->get();


            foreach ($fieldsToDelete as $field) {
                $field->responses()->delete();
                $field->positions()->delete();
                $field->delete();
            }

            // Upsert request fields
            foreach ($request->fields as $field) {
                $options = null;

                if (in_array($field['type'], ['select', 'checkbox', 'radio'])) {
                    if (($field['source'] ?? 'manual') === 'manual') {
                        $options = isset($field['options']) ? json_encode($field['options']) : null;
                    } else {
                        $sourceTable = $field['source'];
                        if (Schema::hasTable($sourceTable)) {
                            $keyColumn   = $field['key_column'] ?? 'id';
                            $valueColumn = $field['value_column'] ?? 'name';

                            $dbOptions = DB::table($sourceTable)
                                ->pluck($valueColumn, $keyColumn)
                                ->toArray();

                            $options = json_encode($dbOptions);
                        }
                    }
                }

                $role->fields()->updateOrCreate(
                    ['id' => $field['id'] ?? null, 'role_id' => $role->id],
                    [
                        'label'        => $field['label'],
                        'name'         => $field['name'],
                        'type'         => $field['type'],
                        'source'       => $field['type'] === 'select' ? ($field['source'] ?? 'manual') : null,
                        'key_column'   => $field['key_column'] ?? null,
                        'value_column' => $field['value_column'] ?? null,
                        'required'     => isset($field['required']) ? 1 : 0,
                        'options'      => $options,
                    ]
                );

                InvitationRecipientField::where('field_id', $field['id'] ?? null)
                    ->update([
                        'field_key' => $field['name'],
                    ]);
            }

            foreach ($additionalFields as $field) {
                InvitationRoleField::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'name' => $field['name'],
                        'source_type' => $field['source_type'],
                    ],
                    [
                        'key_column' => $field['key_column'] ?? null,
                        'label' => $field['label'],
                        'type' => 'text',
                        'required' => true
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => 'Role and invitation fields updated successfully!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['name' => [$e->getMessage()]]], 500);
        }
    }



    /**
     * Return dynamic fields for a given role (AJAX)
     */
    public function getFields(Request $request, InvitationRole $role)
    {
        $fields = $role->fields()
            ->select([
                'id',
                'label',
                'name',
                'type',
                'options',
                'source',
                'required',
                'key_column',
                'value_column'
            ])
            ->where('source_type', 'custom') // filter at DB level
            ->get();

        $fields->transform(function ($field) use ($request) {
            if ($field->source && $field->source !== 'manual') {
                // Dynamic DB source
                if (Schema::hasTable($field->source)) {
                    $keyColumn   = $field->key_column   ?? 'id';
                    $valueColumn = $field->value_column ?? 'name';

                    $query = DB::table($field->source)->select([$keyColumn, $valueColumn]);

                    switch ($field->source) {
                        case 'subjects':
                            if ($request->has('level')) {
                                $level = Level::where('level', $request->level)->first();
                                if ($level) {
                                    $query->where('level', $level->id);
                                }
                            }
                            break;

                        case 'centers':
                            if ($request->has('level')) {
                                $query->where('status', '=', '0')
                                    ->where('level', $request->level);
                            }
                            break;
                    }

                    $field->options = $query->pluck($valueColumn, $keyColumn)->toArray();
                } else {
                    $field->options = [];
                }
            } else {
                // If manual, decode JSON options
                $field->options = $field->options ? json_decode($field->options, true) : [];
            }

            return $field;
        });

        return response()->json($fields);
    }

    public function copyPositions(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'source_role_id' => ['required'],
        ]);

        // Handle errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        };

        $role = InvitationRole::with('fields.positions')->findOrFail($id);
        $sourceRoleId = $request->input('source_role_id');

        $sourceRole = InvitationRole::with('fields.positions')->findOrFail($sourceRoleId);

        foreach ($sourceRole->fields as $sourceField) {
            // Try to find matching field in target role by name
            $field = $role->fields()->where('name', $sourceField->name)->first();
            // If field does not exist, create it (clone)
            if (!$field) {
                $field = $role->fields()->create([
                    'label'        => $sourceField->label,
                    'name'         => $sourceField->name,
                    'type'         => $sourceField->type,
                    'source'       => $sourceField->source,
                    'key_column'   => $sourceField->key_column,
                    'value_column' => $sourceField->value_column,
                    'is_visible' => $sourceField->is_visible,
                    'required'     => $sourceField->required,
                    'options'      => $sourceField->options,
                    'source_type'  => $sourceField->source_type,
                ]);
            } else {

                // Delete old positions for existing field
                $field->positions()->delete();
                $field->update([
                    'label'        => $sourceField->label,
                    'name'         => $sourceField->name,
                    'type'         => $sourceField->type,
                    'source'       => $sourceField->source,
                    'key_column'   => $sourceField->key_column,
                    'value_column' => $sourceField->value_column,
                    'is_visible' => $sourceField->is_visible,
                    'required'     => $sourceField->required,
                    'options'      => $sourceField->options,
                    'source_type'  => $sourceField->source_type,
                ]);
            }

            // Copy positions
            foreach ($sourceField->positions as $pos) {
                $field->positions()->create([
                    'page'   => $pos->page,
                    'pos_x'  => $pos->pos_x,
                    'pos_y'  => $pos->pos_y,
                    'width'  => $pos->width,
                    'height' => $pos->height,
                ]);
            }
        }

        return response()->json([
            'success' => 'Fields and positions copied successfully!',
        ]);
    }

    public function destroy(string $id)
    {
        $role = InvitationRole::find($id);
        // Delete invitation_recipient_fields tied to this role's fields
        InvitationRoleField::where('role_id', $role->id)->each(function ($field) {
            $field->responses()->delete(); // Assuming you have hasMany relation 'responses' to invitation_recipient_fields
        });
        // Delete old role fields
        foreach ($role->fields as $field) {
            $field->positions()->delete();
        }
        $role->fields()->delete();
        $role->delete();
        return response()->json(['success' => 'Role deleted successfully.']);
    }
}
