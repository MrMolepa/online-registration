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

            //FeePaymentHistory

            $invoices = DB::table('fee_candidate_histories')
                ->select(
                    'fee_candidate_histories.*',
                    'fee_groups.name As fee_group',
                    'fee_payment_method.name',
                    'center_candidate.center_no',
                    'center_candidate.candidate_no',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name'
                )
                ->join("fee_payment_method", "fee_payment_method.id", "=", "fee_candidate_histories.pay_via")
                ->join("fee_groups", "fee_groups.id", "=", "fee_candidate_histories.fee_group_id")
                ->join("center_candidate", "center_candidate.id", "=", "fee_candidate_histories.candidate_id")
                ->join("candidates", "candidates.candidate_no", "=", "center_candidate.candidate_no")
                ->where('center_candidate.financial_year', '=', $request->year);
            return DataTables::of($invoices)
                // ->addIndexColumn()
                ->editColumn('created_at', function ($row) {

                    return    $row->created_at;
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
            $services = DB::table('one_time_service_payment_histories')
                ->select(
                    'one_time_service_payment_histories.*',
                    'fee_payment_method.name as payment_method',
                    'clients.first_name',
                    'clients.last_name'
                )
                ->join("fee_payment_method", "fee_payment_method.id", "=", "one_time_service_payment_histories.pay_via")
                ->join("clients", "clients.id", "=", "one_time_service_payment_histories.client_id")
                ->join('one_time_services_item_sale', 'clients.id', '=', 'one_time_services_item_sale.client_id')

                ->where('one_time_services_item_sale.financial_year', '=', $request->year);
            return DataTables::of($services)
                // ->addIndexColumn()
                ->editColumn('created_at', function ($row) {

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
