<?php

namespace App\Http\Controllers\Admin;

use App\Models\InvigilationCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class InvigilationCandidateController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invigilations = InvigilationCandidate::get();
            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.candidatesrange.edit', $row->id)  . '" data-original-title="Edit" class="edit-range btn btn-primary btn-sm fa fa-edit"></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.candidatesrange.destroy', $row->id)   . '" data-original-title="Delete" class="delete-range btn btn-danger btn-sm fa fa-trash"></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.invigilation.candidaterange.index');
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
            'range_start' => 'required|string|max:255',
            'range_end' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        InvigilationCandidate::create([
            'range_start' => $request->range_start,
            'range_end' => $request->range_end,
        ]);

        return response()->json(['success' => 'Candidates range added successfully']);
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
        $invigilation = InvigilationCandidate::find($id);

        $url = route('admin.invigilations.candidatesrange.update', $id);

        return response()->json(['invigilation' => $invigilation, 'url' => $url]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'range_start' => 'required|string',
            'range_end' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        } else {

            $invigilation = InvigilationCandidate::find($id);
            $invigilation->range_start = $request->range_start;
            $invigilation->range_end = $request->range_end;

            $invigilation->save();
        }

        return response()->json(['success' => 'Candidate range update successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        InvigilationCandidate::find($id)->delete();
        return response()->json(['success' => 'Candidate range deleted successfully.']);
    }
}
