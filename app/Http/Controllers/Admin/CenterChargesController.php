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
        $center = Center::findOrFail($request->center_no);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        if ($request->ajax()) {
            $centerOtherCharges = CenterOtherCharge::with('center', 'user')
                ->where('financial_year', '=',  $financial_year)
                ->where('center_id', '=', $request->center_no)->get();

            return DataTables::of($centerOtherCharges)
                // ->addIndexColumn()
                ->editColumn('actions', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn' data-url='" . route('admin.center-charges.edit', $row->id) . "'> Edit</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.center-charges.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        $centerOtherCharges = CenterOtherCharge::with('center', 'user')->where('center_id', '=', $request->center_no)->get();
        return view('admin.finance.othercharges.othercharges', compact('centerOtherCharges', 'center'));
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
            'charge' => 'required|numeric',
            'comments' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        CenterOtherCharge::create([
            'center_id' => $request->center_no,
            'charge' => $request->charge,
            'added_by' => auth()->user()->id,
            'financial_year' => $financial_year,
            'comments' => $request->comments,
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
        $url = route('admin.center-charges.update', $id);
        return response()->json(['centerOtherCharge' => $centerOtherCharge, 'url' => $url]);
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
            'charge' => 'required|numeric',
            'comments' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $centerOtherCharge = CenterOtherCharge::with('center', 'user')->findOrFail($id);
        $centerOtherCharge->charge = $request->charge;
        $centerOtherCharge->added_by = auth()->user()->id;
        $centerOtherCharge->comments = $request->comments;
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
