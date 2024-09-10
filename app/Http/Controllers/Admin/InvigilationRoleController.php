<?php

namespace App\Http\Controllers\Admin;

use App\Models\InvigilationCandidate;
use App\Models\InvigilationRole;
use App\Models\InvigilationType;
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
            $invigilations = InvigilationRole::with('invigilation_type', 'invigilation_candidate');
            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.roles.edit', $row->id)  . '" data-original-title="Edit" class="edit-role btn btn-primary btn-sm fa fa-edit"></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.roles.destroy', $row->id)   . '" data-original-title="Delete" class="delete-role btn btn-danger btn-sm fa fa-trash"></a>';

                    return $btn;
                })
                ->addColumn('candidate_range', function ($row) {
                    return $row->invigilation_candidate->range_start . " - " . $row->invigilation_candidate->range_end;
                })
                ->rawColumns(['action', 'candidate_range'])
                ->make(true);
        }
        $invigilatortypes = InvigilationType::get();
        $invigilatorCandidates = InvigilationCandidate::get();

        return view('admin.invigilation.roles.index', compact('invigilatortypes', 'invigilatorCandidates'));
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
            'amount' => 'required|string',



        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        InvigilationRole::create([
            'invigilation_type_id' => $request->invigilation_type_id,
            'invigilation_candidate_id' => $request->invigilation_candidate_id,
            'invigilator_number' => $request->invigilator_number,
            'amount' => $request->amount,

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
        $validator = Validator::make($request->all(), []);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        } else {

            $invigilation = InvigilationRole::find($id);
            $invigilation->invigilation_type_id = $request->invigilation_type_id;
            $invigilation->invigilation_candidate_id = $request->invigilation_candidate_id;
            $invigilation->invigilator_number = $request->invigilator_number;
            $invigilation->amount = $request->amount;
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
