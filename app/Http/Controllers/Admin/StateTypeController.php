<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StateType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StateTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        if ($request->ajax()) {
            ini_set('memory_limit', '-1');
            set_time_limit(-1);
            $stateTypes=  StateType::query();
            return DataTables::eloquent($stateTypes)
                ->setRowId('id')
                ->editColumn('name', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->name</span>
                    <input class='editInput period form-control' type='text' name='name' value='$row->name'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('description', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->description</span>
                    <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'><i class='fas fa-edit'></i></button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.state-types.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.state-types.destroy', $row->id) . "'> <i class='fas fa-trash-alt'></i></button>";
                    return     $html;
                })
                ->rawColumns(['name','description' ,'action'])
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
