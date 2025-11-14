<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvigilationPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class InvigilationPaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invigilations = InvigilationPaymentMethod::get();
            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('is_account_number', function ($row) {
                    return ($row->is_account_number == '1') ? 'Yes' : 'No';
                })
                ->addColumn('is_bank_name', function ($row) {
                    return ($row->is_bank_name == '1') ? 'Yes' : 'No';
                })
                ->addColumn('is_branch', function ($row) {
                    return ($row->is_branch == '1') ? 'Yes' : 'No';
                })
                ->addColumn('is_phone_number', function ($row) {
                    return ($row->is_payable_phone_number== '1') ? 'Yes' : 'No';
                })
                ->addColumn('is_tin_number', function ($row) {
                    return ($row->is_tin_number == '1') ? 'Yes' : 'No';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.paymentmethods.edit', $row->id)  . '" data-original-title="Edit" class="edit-paymentmethods btn btn-primary btn-sm fa fa-edit"></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.invigilations.paymentmethods.destroy', $row->id)   . '" data-original-title="Delete" class="delete-paymentmethods btn btn-danger btn-sm fa fa-trash"></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.invigilation.paymentmethods.index');
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

        $validator = Validator($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        InvigilationPaymentMethod::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_account_number' => $request->has('is_account_number') ? 1 : 0,
            'is_bank_name' => $request->has('is_bank_name') ? 1 : 0,
            'is_branch' => $request->has('is_branch') ? 1 : 0,
            'is_payable_phone_number' => $request->has('is_payable_phone_number') ? 1 : 0,
            'is_tin_number' => $request->has('is_tin_number') ? 1 : 0,

        ]);

        return response()->json(['success' => 'Paymet method add successfully']);
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
        $invigilation = InvigilationPaymentMethod::find($id);

        $url = route('admin.invigilations.paymentmethods.update', $id);

        return response()->json(['invigilation' => $invigilation, 'url' => $url]);
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
            'name' => 'required|string',
            'description' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        } else {

            $invigilation = InvigilationPaymentMethod::find($id);
            $invigilation->name = $request->name;
            $invigilation->description = $request->description;
            $invigilation->is_account_number = $request->has('is_account_number') ? 1 : 0;
            $invigilation->is_bank_name = $request->has('is_bank_name') ? 1 : 0;
            $invigilation->is_branch = $request->has('is_branch') ? 1 : 0;
            $invigilation->is_payable_phone_number = $request->has('is_payable_phone_number') ? 1 : 0;
            $invigilation->is_tin_number = $request->has('is_tin_number') ? 1 : 0;
            $invigilation->save();
        }







        return response()->json(['success' => 'Payment Method update successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        InvigilationPaymentMethod::find($id)->delete();
        return response()->json(['success' => 'Payment method deleted successfully.']);
    }
}
