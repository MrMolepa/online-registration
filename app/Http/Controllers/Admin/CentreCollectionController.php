<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use App\Libraries\Payment\Payment;
use Illuminate\Support\Facades\Validator;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\CenterPayment;
use Illuminate\Support\Facades\File as FileFacade;




class CentreCollectionController extends Controller
{

    // Payment Verification  centers
    public function index(Request $request)
    {
        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');

        if ($request->ajax()) {
            $centers = Center::query()->whereHas('candidates', function ($query) use ($request) {
                $query->where('financial_year', '=', $request->year);
            });
            return   DataTables::eloquent($centers)
                ->addColumn('actions', function ($model) {
                    $financial_year = "";
                    if (date('m') <= 2) { //Upto June 2014-2015
                        $financial_year = (date('Y') - 1) . '-' . date('Y');
                    } else { //After June 2015-2016
                        $financial_year = date('Y') . '-' . (date('Y') + 1);
                    }
                    $uncheck = CenterPayment::where('status', '=', '0')
                        ->where('center_no', '=', $model->center_no)
                        ->where('financial_year', '=', $financial_year)
                        ->count();
                    $htmlUncheck = ($uncheck > 0) ? "<span class='label label-danger'>$uncheck</span>" : "";
                    $actions = '<div class="btn-group actions">';
                    $actions .= "<a href='" . route('admin.centre-collection.fees.center', $model->center_no) . "'
                                data-toggle='tooltip' title='Proof of payments'
                                class='btn btn-primary'><i class='fas fa-file-invoice-dollar'></i>
                                $htmlUncheck
                             </a>";

                    $actions .= '</div>';
                    return       $actions;
                })->rawColumns(['actions'])
                ->toJson();
        }
        return view('admin.finance.payments-verification.payments-verification', compact(
            'years',
        ));
    }

    public function center_collection(Request $request, $center_no)
    {

        if ($request->ajax()) {

            if ($request->has('invoices')) {
                $levels = DB::table('center_candidate')->select(
                    [
                        'center_candidate.level'
                    ],
                )
                    ->distinct()
                    ->orderBy('level')
                    ->where('financial_year', '=', $request->year)
                    ->where('center_no', '=', $center_no)
                    ->get();


                $html = '';
                $total_paid = 0;
                $total_charges = 0;
                $total_overdue = 0;


                $html = "<table class='table' name='tablename'>
                             <thead>

                            </thead>
                            <tbody>";


                foreach ($levels as $level) {
                    $schoolfees = Payment::schoolfees($center_no, $level->level, "November", $request->year);
                    $sponsor_o= isset($schoolfees['sponsors']['O']['sponsor_overdue'])?$schoolfees['sponsors']['O']['sponsor_overdue']:0;
                    $sponsor_p=isset($schoolfees['sponsors']['P']['sponsor_overdue'])?$schoolfees['sponsors']['P']['sponsor_overdue']:0;
                    $level_overdue =  $sponsor_o +  $sponsor_p;
                    $total_overdue += $level_overdue;
                    $level_total_paid = $schoolfees['total_paid'];
                    $level_charges = $schoolfees['other_charges'];

                    if ($total_paid < $level_total_paid) {
                        $total_paid = $level_total_paid;
                    }
                    if ($total_charges <  $level_charges) {
                        $total_charges =  $level_charges;
                    }


                    $html .= "<tr>
                                 <th colspan='7'>$level->level </th>
                               </tr>
                                <tr>
                                    <th colspan='4'>Total Fees</th>
                                    <td colspan='3'>
                                        LSL " . number_format($level_overdue, 2, '.', '') . "
                                    </td>
                                </tr>

                                ";
                }

                $balance   = $total_overdue +  $total_charges - $total_paid;

                $html .= "
                          <tr>
                             <th colspan='4'>Total Overdue</th>
                                <td colspan='3'>
                                    LSL " . number_format($total_overdue, 2, '.', '') . "
                                </td>
                            </tr>
                            <tr>
                             <th colspan='4'>Total Paid</th>
                                <td colspan='3'>
                                    LSL " . number_format($total_paid, 2, '.', '') . "
                                </td>
                            </tr>
                            <tr>
                             <th colspan='4'>Other Charges</th>
                                <td colspan='3'>
                                    LSL " . number_format($total_charges, 2, '.', '') . "
                                </td>
                            </tr>
                             <tr>
                             <th colspan='4'>Balance</th>
                                <td colspan='3'>
                                    LSL " . number_format($balance, 2, '.', '') . "
                                </td>
                            </tr>
                            </tbody>
                            </table>";

                return response()->json(['html' => $html]);
            }
            $confirmations = CenterPayment::with('center')
                ->where('center_no', '=', $center_no)
                ->where('financial_year', '=', $request->year)->get();
            return DataTables::of($confirmations)
                ->editColumn('status', function ($row) {
                    $status = "";
                    if ($row->status == 1) {
                    } elseif ($row->status == 2) {
                        $status = "<span class='valid-status'></span>";
                    } else {
                        $status = "<span class='not-checked-status'></span>";
                    }
                    return  "$status  $row->created_at";
                })
                ->editColumn('center_no', function ($row) {
                    return    $row->center_no;
                })
                ->editColumn('center_name', function ($row) {
                    return  $row->center->center_name;
                })
                ->editColumn('attachment', function ($row) {
                    $attachment = "";
                    if (!is_null($row->attachment)) {
                        $attachment = "<a href='" . asset($row->attachment) . "'
                                target='_blank'><i class='fas fa-download'  class='btn btn-primary' download></i></a>";
                    } else {
                        $attachment = " <p>Bal b/f</p>";
                    }
                    return  $attachment;
                })
                ->editColumn('amount', function ($row) {
                    return   $row->amount;
                })
                ->editColumn('collect_by', function ($row) {
                    return     $row->collect_by;
                })
                ->editColumn('actions', function ($row) {
                    $html = "<button type='button' title='edit' class='btn btn-sm btn-primary edit-payment'  data-url='" . route('admin.centre-collection.fees.edit', $row->id) . "' > Edit</button>
                            <button data-url='" . route('admin.centre-collection.fees.destroy', $row->id) . "' class='btn btn-sm btn-danger delete-payment'> Delete </button>
                            ";
                    return     $html;
                })
                ->rawColumns(['status', 'center_no', 'center_name', 'attachment', 'amount', 'collect_by', 'actions'])
                ->toJson();
        }

        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');

        $sessions =  CenterCandidate::select(DB::raw('session as session'))
            ->distinct()
            ->get()->pluck('session');
        $center = Center::where('center_no', '=', $center_no)->first();
        return view('admin.finance.payments-verification.center.center-verification', compact('center', 'years', 'sessions'));
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
            'attachment' => 'required',
            'status' => 'required',
            'session' => 'required',
            'financial_year' => 'required',
            'reference_no' => 'required',
            'remarks' => 'required',


        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $attactment = $request->file('attachment')->storeAs('bankStatement', time() . '.' . $request->file('attachment')->getClientOriginalExtension(),  'public');
        if ($attactment == null ||    $attactment == '') {
            return response()->json(['attachment' => ['Error in storing document in local']]);
        }


        $centerPayment = new CenterPayment();
        $centerPayment->center_no = $request->center_no;
        $centerPayment->amount = $request->amount;
        $centerPayment->reference_no = $request->reference_no;
        $centerPayment->attachment = '/storage/' . $attactment;
        $centerPayment->financial_year = $request->financial_year;
        $centerPayment->session = $request->session;
        $centerPayment->status = $request->status;
        $centerPayment->remarks = $request->remarks;
        $centerPayment->collect_by = auth()->user()->email;
        $centerPayment->save();
        return response()->json(['success' => 'Successfully added the records']);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $centerPayment = CenterPayment::findOrFail($id);
        $url = route('admin.centre-collection.fees.update', $id);
        return response()->json(['payment' =>  $centerPayment, 'url' => $url]);
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
            'attachment' => 'required',
            'status' => 'required',
            'session' => 'required',
            'financial_year' => 'required',
            'reference_no' => 'required',
            'remarks' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $centerPayment = CenterPayment::with('center')->findOrFail($id);

        $attactment = $request->file('attachment')->storeAs('bankStatement', time() . '.' . $request->file('attachment')->getClientOriginalExtension(),  'public');
        if ($attactment == null ||    $attactment == '') {
            return response()->json(['attachment' => ['Error in storing document in local']]);
        }



        if ($centerPayment->attachment && !empty($attachment) ) {
            FileFacade::delete(public_path($centerPayment->attachment));
        }

        $centerPayment->center_no = $request->center_no;
        $centerPayment->amount = $request->amount;
        $centerPayment->reference_no = $request->reference_no;
        $centerPayment->attachment = '/storage/' . $attactment;
        $centerPayment->financial_year = $request->financial_year;
        $centerPayment->session = $request->session;
        $centerPayment->status = $request->status;
        $centerPayment->remarks = $request->remarks;
        $centerPayment->collect_by = auth()->user()->email;
        $centerPayment->save();
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

        $centerPayment = CenterPayment::findOrFail($id);

        if ($centerPayment->attachment) {
            FileFacade::delete(public_path($centerPayment->attachment));
        }
        $centerPayment->delete();
        return response()->json(['success' => 'Successfully deleted the records']);
    }
}
