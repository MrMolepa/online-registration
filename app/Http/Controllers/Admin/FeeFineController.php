<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeFine;
use App\Models\FeeGroup;
use App\Models\FeeLateFrequency;
use App\Models\FeeType;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FeeFineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $fines = FeeFine::with('feegroups','feetypes','frequencies','feegroups.session');
            return DataTables::of($fines)
                ->addIndexColumn()
                ->addColumn('fee_group_id', function ($row) {
                        return $row->feegroups->name." - ".$row->feegroups->session->session." - ".$row->feegroups->session->financial_year;
                 })
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.fines.edit', $row->id)  . '" data-original-title="Edit" class="edit-fine  btn btn-primary btn-sm fa fa-edit"></a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.fines.destroy', $row->id)   . '" data-original-title="Delete" class="delete-fine btn btn-danger btn-sm  fa fa-trash"></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
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
            'fee_group_id' => 'required|string',
            'fee_type_id' => 'required|string',
            'fine_type' => 'required|string',
            'fine_value' => 'required|string',
            'fee_frequency_id' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            FeeFine::create([
                'fee_group_id' => $request->fee_group_id,
                'fee_type_id' => $request->fee_type_id,
                'fine_type' => $request->fine_type,
                'fine_value' => $request->fine_value,
                'fee_frequency_id' => $request->fee_frequency_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            return response()->json(['success' => "Fine Fee added successfully"]);
        }
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
        $fine = FeeFine::find($id);
        $url = route('admin.fees-stracture.fines.update', $id);
        return response()->json(['fine' => $fine, 'url' => $url]);
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
            'fee_group_id' => 'required|string',
            'fee_type_id' => 'required|string',
            'fine_type' => 'required|string',
            'fine_value' => 'required|string',
            'fee_frequency_id' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            $fine=FeeFine::find($id);
            $fine->fee_group_id = $request->fee_group_id;
            $fine->fee_type_id = $request->fee_type_id;
            $fine->fine_type = $request->fine_type;
            $fine->fine_value = $request->fine_value;
            $fine->fee_frequency_id = $request->fee_frequency_id;
            $fine->start_date = $request->start_date;
            $fine->end_date = $request->end_date;
            $fine->save();

            return response()->json(['success' => "Fine Fee Updated successfully"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FeeFine::find($id)->delete();
        return response()->json(['success' => 'Fine Fee deleted successfully.']);
    }
}
