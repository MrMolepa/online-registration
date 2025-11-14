<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeeTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $feetype = FeeType::all();
            return DataTables::of($feetype)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.types.edit', $row->id)  . '" data-original-title="Edit" class="editBtn  btn btn-primary btn-sm fa fa-edit"></a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.types.destroy', $row->id)   . '" data-original-title="Delete" class="deleteBtn btn btn-danger  btn-sm fa fa-trash"></a>';
                    return $btn;
                })

                ->rawColumns(['action'])
                ->make(true);
        }
     
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'fee_code' => 'required|max:100|alpha_dash|unique:fee_types,fee_code',
            'fee_name' => 'required|max:100|',
            'fee_description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        FeeType::create([
            'fee_code' => $request->fee_code,
            'fee_name' => $request->fee_name,
            'fee_description' => $request->fee_description,
        ]);

        return response()->json(['success' => 'Fee Type added successfully!']);
    }
    public function edit($id)
    {
        $feetype = FeeType::all()->find($id);
        $url = route('admin.fees-stracture.types.update', $id);

        return response()->json(['feetype' => $feetype, 'url' => $url]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'fee_code' => "required|max:100|alpha_dash|unique:fee_types,fee_code,$id",
            'fee_name' => 'required',
            'fee_description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        } else {

            $feetype = FeeType::find($id);
            $feetype->fee_code = $request->fee_code;
            $feetype->fee_name = $request->fee_name;
            $feetype->fee_description = $request->fee_description;
            $feetype->save();
        }

        return response()->json(['success' => 'Fee Type update successfully']);
    }

    public function destroy($id)
    {
        FeeType::find($id)->delete();
        return response()->json(['success' => 'Fee Type deleted successfully.']);
    }
}
