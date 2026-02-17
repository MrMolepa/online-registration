<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discipline;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DisciplineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $disciplines= Discipline::query();
            return DataTables::eloquent( $disciplines)
                ->setRowId('id')
                ->editColumn('name', function ($row) {
                    $html = "
                <div class='form-group'>
                <span class='editSpan period'> $row->name</span>
                <input class='editInput period form-control' type='text' name='name' value='$row->name'>
                </div>";
                    return     $html;
                })
                ->editColumn('display_name', function ($row) {
                    $html = "<div class='form-group'>
                <span class='editSpan period'> $row->display_name</span>
                <input class='editInput period form-control' type='text' name='display_name' value='$row->display_name'>
                </div>";
                    return     $html;
                    
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                          <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.disciplines.update', $row->id) . "'> Save</button>
                          <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.disciplines.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['name', 'display_name',  'action'])
                ->toJson();
        }
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
            'name' => 'required',
            'display_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $discipline = new Discipline();
        $discipline->name = $request->name;
        $discipline->display_name = $request->display_name;
        $discipline->save();
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
        $discipline = Discipline::findOrFail($id);
        return view('admin.disciplines.edit', compact('discipline'));
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
            'name' => 'required',
            'display_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $discipline = Discipline::findOrFail($id);
        $discipline->name = $request->name;
        $discipline->display_name = $request->display_name;
        $discipline->save();
        return response()->json(['success' =>  $discipline]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $discipline = Discipline::findOrFail($id);
        $discipline->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
