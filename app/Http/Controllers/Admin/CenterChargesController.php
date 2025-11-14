<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\CenterOtherCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CenterChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $financial_year = $request->year;
        if ($request->ajax()) {
            $centerOtherCharges = CenterOtherCharge::with('center')
                ->where('financial_year', '=',  $financial_year)
                ->where('center_no', '=', $request->center_no)->get();

            return DataTables::of($centerOtherCharges)
                // ->addIndexColumn()
                ->editColumn('actions', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary edit-charge' data-url='" . route('admin.centre-collection.center-charges.edit', $row->id) . "'> Edit</button>
                              <button type='button' class='btn btn-sm btn-danger delete-charge' data-url='" . route('admin.centre-collection.center-charges.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['actions'])
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
            'center_no' => 'required',
            'amount' => 'required|numeric',
            'session' => 'required',
            'financial_year' => 'required',
            'remarks' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        CenterOtherCharge::create([
            'center_no' => $request->center_no,
            'amount' => $request->amount,
            'financial_year' => $request->financial_year,
            'session' =>  $request->session,
            'collected_by' => auth()->user()->email,
            'remarks' => $request->remarks,
        ]);
        return response()->json(['success' => 'Successfully added the records']);
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
        $centerOtherCharge = CenterOtherCharge::findOrFail($id);
        $url = route('admin.centre-collection.center-charges.update', $id);
        return response()->json(['charge' => $centerOtherCharge, 'url' => $url]);
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
            'center_no' => 'required',
            'amount' => 'required|numeric',
            'session' => 'required',
            'financial_year' => 'required',
            'remarks' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $centerOtherCharge = CenterOtherCharge::with('center')->findOrFail($id);
        $centerOtherCharge->center_no = $request->center_no;
        $centerOtherCharge->amount = $request->amount;
        $centerOtherCharge->financial_year = $request->financial_year;
        $centerOtherCharge->session = $request->session;
        $centerOtherCharge->collected_by = auth()->user()->email;
        $centerOtherCharge->remarks = $request->remarks;
        $centerOtherCharge->save();
        return response()->json(['success' => 'Successfully updated the records']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $centerOtherCharge = CenterOtherCharge::findOrFail($id);
        $centerOtherCharge->delete();
        return response()->json(['success' => 'Successfully deleted the records']);
    }
}
