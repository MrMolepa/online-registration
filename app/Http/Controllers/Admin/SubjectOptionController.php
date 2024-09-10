<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OptionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SubjectOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        if ($request->ajax()) {
            $optionHeaders = OptionHeader::query();
            return DataTables::eloquent($optionHeaders)
                ->setRowId('option_code')
                ->editColumn('option_code', function ($row) {
                    $html = "
                <div class='form-group'>
                <span class='editSpan period'>$row->option_code</span>
                <input class='editInput period form-control' type='text' name='option_code' value='$row->option_code'>
                </div>";
                    return     $html;
                })
                ->editColumn('alternative_option_code', function ($row) {
                    $html = "
                <div class='form-group'>
                <span class='editSpan period'> $row->alternative_option_code</span>
                <input class='editInput period form-control' type='text' name='alternative_option_code' value='$row->alternative_option_code'>
                </div>";
                    return     $html;
                })
                ->editColumn('description', function ($row) {
                    $html = "<div class='form-group'>
                                <span class='editSpan period'> $row->description</span>
                                <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                             </div>";
                    return $html;
                })->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                          <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.options.update', $row->option_code) . "'> Save</button>
                          <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.options.destroy', $row->option_code) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['level', 'description', 'alternative_option_code', 'option_code', 'action'])
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
            'option_code' => 'required',
            'alternative_option_code' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $optionHeader = new OptionHeader();
        $optionHeader->option_code= $request->option_code;
        $optionHeader->alternative_option_code = $request->alternative_option_code;
        $optionHeader->description = $request->description;
        $optionHeader->save();
        return response()->json(['success' =>  $optionHeader]);
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
            'option_code' => 'required',
            'alternative_option_code' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $optionHeader = OptionHeader::findOrFail($id);
        $optionHeader->option_code = $request->option_code;
        $optionHeader->alternative_option_code = $request->alternative_option_code;
        $optionHeader->description = $request->description;
        $optionHeader->save();
        return response()->json(['success' =>  $optionHeader]);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $optionHeader = OptionHeader::findOrFail($id);
        $optionHeader->delete();
        return response()->json(['success' =>  'You have successfully deleted  the records']);
    }
}
