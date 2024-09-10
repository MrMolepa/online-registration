<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OneTimeService;
use App\Models\ServiceAttribute;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ServiceRequirementContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $frontend_types = getEnumValues('services_attributes', 'frontend_type');
        $oneTimeService = OneTimeService::findOrFail($request->service);
        if ($request->ajax()) {
            $serviceAttributes = ServiceAttribute::where(['one_time_service_id' => $request->service]);
            return DataTables::eloquent($serviceAttributes)
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
                ->editColumn('code', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->code</span>
                    <input class='editInput period form-control' type='text' name='code' value='$row->code'>
                    </div>";
                    return     $html;
                })
                ->editColumn('placeholder', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->placeholder</span>
                    <input class='editInput period form-control' type='text' name='placeholder' value='$row->placeholder'>
                    </div>";
                    return     $html;
                })
                ->editColumn('frontend_type', function ($row) use ($frontend_types) {
                    $options = "";
                    foreach ($frontend_types as $frontend_type) {
                        $selected = $frontend_type == $row->frontend_type ? "selected" : " ";
                        $options .= " <option value='$frontend_type'  $selected >$frontend_type</option>";
                    }

                    $html = "<div class='form-group'>
                                <span class='editSpan period'> $row->frontend_type</span>
                                <select class='form-control editInput period' name='frontend_type' id='frontend_type'>
                                $options
                                </select>
                            </div>";

                    return  $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.service-requirements.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.service-requirements.destroy', $row->id) . "'> Delete</button>";
                    return   $html;
                })
                ->rawColumns(['name', 'code', 'placeholder', 'frontend_type', 'action'])
                ->make(true);
        }
        return view('admin.services.serviceRequirements', compact('oneTimeService', 'frontend_types'));
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
            'code' => 'required',
            'placeholder' => 'required',
            'frontend_type' => 'required',
            'service' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $serviceAttribute = new ServiceAttribute();
        $serviceAttribute->one_time_service_id = $request->service;
        $serviceAttribute->code = $request->code;
        $serviceAttribute->name = $request->name;
        $serviceAttribute->placeholder = $request->placeholder;
        $serviceAttribute->frontend_type = $request->frontend_type;
        $serviceAttribute->is_required = $request->is_required;
        $serviceAttribute->save();
        return response()->json(['success' => $serviceAttribute]);
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
            'name' => 'required',
            'code' => 'required',
            'placeholder' => 'required',
            'frontend_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $serviceAttribute = ServiceAttribute::findOrFail($id);
        $serviceAttribute->code = $request->code;
        $serviceAttribute->name = $request->name;
        $serviceAttribute->frontend_type = $request->frontend_type;
        $serviceAttribute->placeholder = $request->placeholder;
        $serviceAttribute->save();
        return response()->json(['success' => $serviceAttribute]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $serviceAttribute = ServiceAttribute::findOrFail($id);
        $serviceAttribute->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
