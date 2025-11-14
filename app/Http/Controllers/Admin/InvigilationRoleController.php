<?php

namespace App\Http\Controllers\Admin;

use App\Models\InvigilationCandidate;
use App\Models\InvigilationCatergories;
use App\Models\InvigilationRole;
use App\Models\InvigilationType;
use App\Models\InvigilatorPaymentAmount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class InvigilationRoleController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invigilations = InvigilationRole::with('invigilation_type.invigilation_catergories', 'invigilation_candidate', 'invigilator_paymentamount');
            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.roles.edit', $row->id)  . '" data-original-title="Edit" class="edit-role btn-primary  btn-sm fa fa-edit"></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.roles.destroy', $row->id)   . '" data-original-title="Delete" class="delete-role btn btn-danger btn-sm fa fa-trash"></a>';

                    return $btn;
                })

                ->addColumn('invigilation_type', function ($row) {
                    return $row->invigilation_type->name ."-".$row->invigilation_type->invigilation_catergories->name;
                })
                ->addColumn('is_sessions', function ($row) {
                    return ($row->is_sessions == '1') ? 'Yes' : 'No';
                })
                ->addColumn('candidate_range', function ($row) {
                    return isset($row->invigilation_candidate->range_start) ? $row->invigilation_candidate->range_start . " - " . $row->invigilation_candidate->range_end : 'session_based';
                })
                ->rawColumns(['action', 'candidate_range'])
                ->make(true);
        }

        $invigilatortypes = InvigilationType::with("invigilation_catergories")->get();
        $invigilatorCandidates = InvigilationCandidate::get();
        $invigilatorPaymentamounts = InvigilatorPaymentAmount::get();

        return view('admin.invigilation.roles.index', compact('invigilatortypes', 'invigilatorCandidates', 'invigilatorPaymentamounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'invigilation_type_id' => 'required|string',
            'invigilation_candidate_id' => 'required|string',
            'invigilator_number' => 'required|string',
            'invigilator_paymentamount_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        InvigilationRole::create([
            'invigilation_type_id' => $request->invigilation_type_id,
            'invigilation_candidate_id' => $request->invigilation_candidate_id,
            'invigilator_number' => $request->invigilator_number,
            'invigilator_paymentamount_id' => $request->invigilator_paymentamount_id,

            'is_sessions' => $request->has('is_sessions') ? 1 : 0,

        ]);

        return response()->json(['success' => 'Roles  successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invigilation = InvigilationRole::find($id);

        $url = route('admin.invigilations.roles.update', $id);

        return response()->json(['invigilation' => $invigilation, 'url' => $url]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'invigilation_type_id' => 'required|string',
            'invigilation_candidate_id' => 'required|string',
            'invigilator_number' => 'required|string',
            'invigilator_paymentamount_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        } else {

            $invigilation = InvigilationRole::find($id);
            $invigilation->invigilation_type_id = $request->invigilation_type_id;
            $invigilation->invigilation_candidate_id = $request->invigilation_candidate_id;
            $invigilation->invigilator_number = $request->invigilator_number;
            $invigilation->invigilator_paymentamount_id = $request->invigilator_paymentamount_id;
            $invigilation->is_sessions = $request->has('is_sessions') ? 1 : 0;
            $invigilation->save();
        }

        return response()->json(['success' => 'Roles update successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        InvigilationRole::find($id)->delete();
        return response()->json(['success' => 'Role deleted successfully.']);
    }
}
