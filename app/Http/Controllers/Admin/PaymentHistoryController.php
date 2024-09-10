<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenterCandidate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PaymentHistoryController extends Controller
{
    //


    public function index(Request $request)
    {

      
      
   


        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
    

  



        if ($request->ajax()) {


            $invoices = DB::table('invoices')
                 ->select('invoices.*','candidates.candidate_surname','candidates.candidate_other_name')
                ->join("candidates", "candidates.candidate_no", "=", "invoices.client_id", "left")
                ->where('invoices.financial_year','=', $request->year)
                ->get();
            return DataTables::of($invoices)
                // ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    $date = date('Y-m-d', strtotime($row->created_at));
                    return    $date;
                })
                ->editColumn('amount', function ($row) {
                    $amount = 'LSL ' . number_format($row->amount, 2, '.', '');
                    return $amount;
                })
                ->rawColumns(['created_at', 'amount'])
                ->make(true);
        }
        return view('admin.finance.payment-history.history', compact('years'));
    }


    public function paymentHistory(Request $request)
    {
        if ($request->ajax()) {

            $payments = Payment::get();
            return DataTables::of($payments)
                // ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    $date = date('Y-m-d', strtotime($row->created_at));
                    return   $row->created_at;
                })
                ->editColumn('amount', function ($row) {
                    $amount = 'LSL ' . number_format($row->amount, 2, '.', '');
                    return $amount;
                })
                ->rawColumns(['created_at', 'amount'])
                ->make(true);
        }
    }
}
