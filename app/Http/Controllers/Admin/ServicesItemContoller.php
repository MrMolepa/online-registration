<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OneTimeService;
use App\Models\OneTimeServicesItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ServicesItemContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $oneTimeService = OneTimeService::findOrFail($request->service);
        if ($request->ajax()) {

            $oneTimeServiceItems = OneTimeServicesItem::where(['one_time_service_id' => $request->service]);
            return DataTables::eloquent($oneTimeServiceItems)
                ->setRowId('id')
                ->editColumn('id', function ($row) {
                    return  $row->id;
                })
                ->editColumn('name', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->name</span>
                    <input class='editInput period form-control' type='text' name='name' value='$row->name'>
                    </div>";
                    return     $html;
                })
                ->editColumn('description', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'> $row->description</span>
                    <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                    </div>";
                    return     $html;
                })
                ->editColumn('financial_year', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'> $row->financial_year</span>
                    <input class='editInput period form-control' type='text' name='financial_year' value='$row->financial_year'>
                    </div>";
                    return     $html;
                })
                ->editColumn('price', function ($row) {
                    $html = "<div class='form-group'>
                    <span class='editSpan period'> $row->price</span>
                    <input class='editInput period form-control' type='text' name='price' value='$row->price'>
                    </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.service-item.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.service-item.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['name', 'description', 'financial_year', 'price', 'action'])
                ->make(true);
        }
        return view('admin.services.serviceItem', compact('oneTimeService', 'financial_year'));
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
            'description' => 'required',
            'financial_year' => 'required',
            'price' => 'required',
            'service' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        //  `one_time_service_id`, `name`, `description`, `financial_year`, `price`,

        $oneTimeServiceitem = new OneTimeServicesItem();
        $oneTimeServiceitem->one_time_service_id = $request->service;
        $oneTimeServiceitem->name = $request->name;
        $oneTimeServiceitem->description = $request->description;
        $oneTimeServiceitem->financial_year = $request->financial_year;
        $oneTimeServiceitem->price = $request->price;
        $oneTimeServiceitem->save();
        return response()->json(['success' =>    $oneTimeServiceitem]);
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
            'description' => 'required',
            'financial_year' => 'required',
            'price' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        //  `one_time_service_id`, `name`, `description`, `financial_year`, `price`,
        $oneTimeServiceitem =  OneTimeServicesItem::findOrFail($id);
        $oneTimeServiceitem->name = $request->name;
        $oneTimeServiceitem->description = $request->description;
        $oneTimeServiceitem->financial_year = $request->financial_year;
        $oneTimeServiceitem->price = $request->price;
        $oneTimeServiceitem->save();
        return response()->json(['success' =>  $oneTimeServiceitem]);
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

        $oneTimeServiceitem =  OneTimeServicesItem::findOrFail($id);
        $oneTimeServiceitem->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
