<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ServiceEmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $emails  = ServiceEmail::where('one_time_service_id',$request->service);
            return DataTables::eloquent( $emails)
                ->setRowId('id')
                ->editColumn('id', function ($row) {
                    return  $row->id;
                })
                ->editColumn('email', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->email</span>
                    <input class='editInput period form-control' type='text' name='email' value='$row->email'>
                    </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.service-emails.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.service-emails.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['email', 'action'])
                ->toJson();
        }
        return view('admin.services.services');
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
            'email' => 'required|email',
            'service' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $serviceEmail = new ServiceEmail();
        $serviceEmail->email = $request->email;
        $serviceEmail->one_time_service_id = $request->service;
        $serviceEmail->save();
        return response()->json(['success' => 'You have successfully saved  the records']);
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
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $serviceEmail = ServiceEmail::findOrFail($id);
        $serviceEmail->email = $request->email;
        $serviceEmail->save();
        return response()->json(['success' => 'You have successfully updated  the records']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $serviceEmail = ServiceEmail::findOrFail($id);
        $serviceEmail->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
