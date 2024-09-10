<?php

namespace App\Http\Controllers\Admin;

use App\Models\InvigilationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class InvigilationTypeController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $invigilations = InvigilationType::get();
            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.types.edit', $row->id)  . '" data-original-title="Edit" class="edit-type btn btn-primary btn-sm fa fa-edit"></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.types.destroy', $row->id)   . '" data-original-title="Delete" class="delete-type btn btn-danger btn-sm fa fa-trash"></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.invigilation.type.index');
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
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        InvigilationType::create([
            'name' => $request->name,
        ]);

        return response()->json(['success' => "Invigilation type successfully added"]);
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
        $invigilation = InvigilationType::find($id);

        $url = route('admin.invigilations.types.update', $id);

        return response()->json(['invigilation' => $invigilation, 'url' => $url]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->toArray()]);
        } else {

            $invigilation = InvigilationType::find($id);
            $invigilation->name = $request->name;
            $invigilation->save();
        }

        return response()->json(['success' => 'Invigilation Type update successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        InvigilationType::find($id)->delete();
        return response()->json(['success' => 'Invigilation Type deleted successfully.']);
    }
}
