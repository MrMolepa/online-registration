<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LateFee;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LateFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $fees =LateFee::get();
            // `start_date`, `end_date`, `amount`, `session`, `financial_year`,
            return DataTables::of($fees)
                ->setRowId('id')
                ->editColumn('start_date', function ($row) {
                    $newDate = date('Y-m-d', strtotime($row->start_date));
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->start_date</span>
                    <input class='editInput period form-control' type='date' name='start_date' value='$newDate'>
                    </div>
                    ";
                    return     $html;
                })

                ->editColumn('end_date', function ($row) {
                    $newDate = date('Y-m-d', strtotime($row->end_date));
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->end_date</span>
                    <input class='editInput period form-control' type='date' name='end_date' value='$newDate'>
                    </div>";
                    return     $html;
                })
                ->editColumn('amount', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->amount</span>
                    <input class='editInput period form-control' type='text' name='amount' value='$row->amount'>
                    </div>";
                    return     $html;
                })
                ->editColumn('session', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->session</span>
                    <input class='editInput period form-control' type='text' name='session'' value='$row->session'>
                    </div>";
                    return     $html;
                })
                ->editColumn('financial_year', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->financial_year</span>
                    <input class='editInput period form-control' type='text' name='financial_year' value='$row->financial_year'>
                    </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'> Edit</button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.late-fees.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.late-fees.destroy', $row->id) . "'> Delete</button>";
                    return     $html;
                })
                ->rawColumns([
                    'start_date',
                    'end_date',
                    'amount',
                    'session',
                    'financial_year',
                    'action'
                ])
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
            'start_date' =>  ['required','date_format:Y-m-d'],
            'end_date' =>  ['required','date_format:Y-m-d'],
            'session' =>  ['required'],
            'amount' =>  ['required','numeric'],
            'financial_year' =>  ['required']
        ]);



        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $latefee =new LateFee();
        $latefee->start_date =date("Y-m-d", strtotime($request->start_date));
        $latefee->end_date = date("Y-m-d", strtotime($request->end_date));
        $latefee->amount =$request->amount;
        $latefee->session =$request->session;
        $latefee->financial_year =$request->financial_year;
        $latefee->save();
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
            'start_date' =>  ['required','date_format:Y-m-d'],
            'end_date' =>  ['required','date_format:Y-m-d'],
            'session' =>  ['required'],
            'amount' =>  ['required','numeric'],
            'financial_year' =>  ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $latefee =LateFee::find($id);
        $latefee->start_date =$request->start_date;
        $latefee->end_date =$request->end_date;
        $latefee->amount =$request->amount;
        $latefee->session =$request->session;
        $latefee->financial_year =$request->financial_year;
        $latefee->save();
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
        $latefee =LateFee::find($id);
        $latefee->delete();
        return response()->json(['success' => 'Successfully deleted the records']);
    }
}
