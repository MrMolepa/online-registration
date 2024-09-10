<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $components  =  Component::where('subject_code', $request->subject_code)->get();
            return DataTables::of($components)
                ->setRowId('id')
                ->editColumn('subject_code', function ($row) {
                    return   str_pad($row->subject_code, 4, '0', STR_PAD_LEFT);
                })
                ->editColumn('component_code', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->component_code</span>
                    <input class='editInput period form-control' type='text' name='component_code' value='$row->component_code'>
                    </div>";
                    return     $html;
                })
                ->editColumn('component_name', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'> $row->component_name</span>
                    <input class='editInput period form-control' type='text' name='component_name' value='$row->component_name'>
                    </div>";
                    return     $html;
                })

                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.components.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.components.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['component_code', 'component_name', 'action'])
                ->make(true);
        }
        $subject_code = $request->subject_code;
        return view('admin.subjects.components', compact('subject_code'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_code' => 'required|max:4',
            'component_code' => 'required',
            'component_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        Component::create([
            'subject_code' => $request->subject_code,
            'component_code' => $request->component_code,
            'component_name' => $request->component_name,

        ]);
        return response()->json(['success' =>  'You have successfully added new records']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'component_code' => 'required',
            'component_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $component = Component::findOrFail($id);
        $component->component_code = $request->component_code;
        $component->component_name = $request->component_name;
        $component->save();
        return response()->json(['success' =>  $component]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $component = Component::findOrFail($id);
        $component->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
