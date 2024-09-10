<?php

namespace App\Http\Controllers\School;

use App\Libraries\Payment\Payment;
use App\Mail\InvigilatorMail;
use App\Models\Center;
use App\Models\InvigilationType;
use App\Models\InvigilatorProfile;
use App\Models\Session;
use App\Notifications\InvigilatorNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvigilatorController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $center_no = auth()->user()->center_no;
            $invigilations = InvigilatorProfile::with('invigilation_role.invigilation_type')->where('center_no', $center_no);
            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {


                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('center.invigilators.edit', $row->id)   . '" data-original-title="Edit" class="edit-paymentmethods  fa fa-edit"></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('center.invigilators.destroy', $row->id)  . '" data-original-title="Delete" class="delete-paymentmethods fa fa-trash"></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }


        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        $candidates = Payment::schoolfees($center_no, $center->level,  $session->session, $session->financial_year);


        $totalcandidates = $candidates['total_candidates'];


        $list_rules = DB::table('invigilation_roles')
            ->select(
                'invigilation_types.name',
                'invigilation_roles.id',
                'invigilation_roles.invigilation_type_id',
                DB::raw("COUNT(invigilation_roles.invigilation_type_id) as number_of_invigilators"),
                'invigilation_roles.invigilator_number',
                DB::raw("CASE WHEN COUNT(invigilation_roles.invigilation_type_id) >= invigilation_roles.invigilator_number
            THEN '1'
            ELSE '0'
          END
          AS is_full")
            )
            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->leftJoin('invigilator_profile', 'invigilator_profile.invigilation_role_id', '=', 'invigilation_roles.id')
            ->groupBy('invigilation_roles.invigilation_type_id')
            ->where('center_no', '=', $center_no)
            ->having(DB::raw("CASE WHEN COUNT(invigilation_roles.invigilation_type_id) >= invigilation_roles.invigilator_number
                    THEN '1'
                    ELSE '0'
                END"), '=', 0)
            ->get()->pluck('id')->toArray();














        $list = DB::table('invigilation_roles')
            ->select(
                'invigilation_types.name',
                'invigilation_roles.id',
                'invigilation_roles.invigilation_type_id',

                DB::raw("COUNT(invigilation_roles.invigilation_type_id) as number_of_invigilators"),
                'invigilation_roles.invigilator_number',
                DB::raw("CASE WHEN COUNT(invigilation_roles.invigilation_type_id) >= invigilation_roles.invigilator_number
            THEN '1'
            ELSE '0'
          END
          AS is_full")
            )

            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->leftJoin('invigilator_profile', 'invigilator_profile.invigilation_role_id', '=', 'invigilation_roles.id')
            ->groupBy('invigilation_roles.invigilation_type_id')
            ->where('center_no', '=', $center_no)
            ->having(DB::raw("CASE WHEN COUNT(invigilation_roles.invigilation_type_id) >= invigilation_roles.invigilator_number
                    THEN '1'
                    ELSE '0'
                END"), '=', 0)
            ->get();






        $invigilation_types = DB::table('invigilation_candidates')
            ->select(
                'invigilation_candidates.range_start',
                'invigilation_candidates.range_end',
                'invigilation_types.name',
                'invigilation_roles.id',
                'invigilation_roles.invigilator_number'
            )
            ->leftJoin('invigilation_roles', 'invigilation_roles.invigilation_candidate_id', '=', 'invigilation_candidates.id')
            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->where('invigilation_candidates.range_start', '<=', $totalcandidates)
            ->where('invigilation_candidates.range_end', '>=', $totalcandidates)
            ->get();



        return view('school.invigilator.index', compact('invigilation_types', 'totalcandidates', 'list_rules'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $center_no = auth()->user()->center_no;
        $list_rules = DB::table('invigilation_roles')
            ->select(
                'invigilation_types.name',
                'invigilation_roles.id',
                'invigilation_roles.invigilation_type_id',
                DB::raw("COUNT(invigilation_roles.invigilation_type_id) as number_of_invigilators"),
                'invigilation_roles.invigilator_number',
                DB::raw("CASE WHEN COUNT(invigilation_roles.invigilation_type_id) >= invigilation_roles.invigilator_number
            THEN '1'
            ELSE '0'
          END
          AS is_full")
            )
            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->leftJoin('invigilator_profile', 'invigilator_profile.invigilation_role_id', '=', 'invigilation_roles.id')
            ->groupBy('invigilation_roles.invigilation_type_id')
            ->where('center_no', '=', $center_no)
            ->having(DB::raw("CASE WHEN COUNT(invigilation_roles.invigilation_type_id) >= invigilation_roles.invigilator_number
                    THEN '1'
                    ELSE '0'
                END"), '=', 0)
            ->get()->pluck('id')->toArray();



        $validator = Validator::make($request->all(), [
            'invigilation_role_id' => ['required'],
            'national_id' => 'required|number',
            'surname' => 'required|string',
            'other_names' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|number',
        ]);
        $validation_messages = [];
        $validator = Validator::make($request->all(), $validation_messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $token = md5(uniqid());


        $invigilation = InvigilatorProfile::create([
            'invigilation_role_id' => $request->invigilation_role_id,
            'national_id' => $request->national_id,
            'surname' => $request->surname,
            'other_names' => $request->other_names,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'token' => $token,
            'center_no' => $center_no,
        ]);

        // send email
        $url = route('applications.index', $token);

        $data = ['center_no' => $center_no, 'other_names' => $request->other_names, 'surname' => $request->surname, 'url' => $url];
        Mail::to($request->email)->send(new InvigilatorMail($data));

        return response()->json(['success' => 'Invigilator add successfully']);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invigilation = InvigilatorProfile::find($id);

        $url = route('center.invigilators.update', $id);

        return response()->json(['invigilation' => $invigilation, 'url' => $url]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'invigilation_role_id' => 'required|string',
            'national_id' => 'required|string',
            'surname' => 'required|string',
            'other_names' => 'required|string',
            'gender' => 'required|string',
            'date_of_birth' => 'required|string',
            'qualification' => 'required|string',
            'email' => 'required|string',
            'phone_number' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        } else {

            $invigilation = InvigilatorProfile::find($id);
            $invigilation->invigilation_role_id = $request->invigilation_role_id;
            $invigilation->national_id = $request->national_id;
            $invigilation->surname = $request->surname;
            $invigilation->other_names = $request->other_names;
            $invigilation->email = $request->email;
            $invigilation->phone_number = $request->phone_number;
            $invigilation->save();
        }

        return response()->json(['success' => 'Invigilator update successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        InvigilatorProfile::find($id)->delete();
        return response()->json(['success' => 'Invigilator deleted successfully.']);
    }
}
