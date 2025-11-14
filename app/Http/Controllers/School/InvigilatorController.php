<?php

namespace App\Http\Controllers\School;

use App\Libraries\Payment\Payment;
use App\Mail\InvigilatorMail;
use App\Models\Center;
use App\Models\InvigilationStatus;
use App\Models\InvigilationType;
use App\Models\InvigilatorExperience;
use App\Models\InvigilatorProfile;
use App\Models\Session;
use App\Notifications\InvigilatorNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isNull;

class InvigilatorController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $center = Center::with('subjects')->where('center_no', '=', auth()->user()->center_no)->first();
        $centerSessions = json_decode($center->sessions, true);

        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();
        $center_no = $center->center_no;

        if ($request->ajax()) {

            $invigilations = InvigilatorProfile::with('invigilation_role.invigilation_type', 'invigilator_experience', 'invigilation_status')
                ->where('session',  $session->session)
                ->where('financial_year',  $session->financial_year)
                ->where('center_no', $center_no);

            return DataTables::of($invigilations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $status = $row->invigilation_status;
                    switch ($status->name) {
                        case 'Accepted':
                            return '';
                            break;
                        case 'Pending':
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('center.invigilators.edit', $row->id)  . '" data-original-title="Edit" class="edit-center btn btn-primary btn-sm fa fa-edit"></a>';
                            $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('center.invigilators.destroy', $row->id)   . '" data-original-title="Delete" class="delete-center btn btn-danger btn-sm fa fa-trash"></a>';
                            return $btn;
                            break;
                        case 'Declined':
                            return '';
                            break;

                            break;
                    }
                })
                ->addColumn('status', function ($row) {

                    $status = $row->invigilation_status;

                    return "<div class='status-tag auxiliar-low highlight' style='background-color:RGBA($status->color_red,$status->color_green,$status->color_blue, .7);color:#fff;'>

                        </div>";
                })->rawColumns(['action', 'status'])
                ->make(true);
        }


        $status = InvigilationStatus::where('status', '=', 0)
            ->orderBy('order_status', 'ASC')->first();



        $totalcandidates = Payment::highestSubject($center_no, $center->level,  $session->session, $session->financial_year);



        $invigilation_types = DB::table('invigilation_candidates')
            ->select(
                'invigilation_candidates.range_start',
                'invigilation_candidates.range_end',
                'invigilation_types.name',
                DB::raw("invigilation_catergories.name as catergory_name"),
                'invigilation_roles.id',
                'invigilation_roles.invigilator_number'
            )
            ->leftJoin('invigilation_roles', 'invigilation_roles.invigilation_candidate_id', '=', 'invigilation_candidates.id')
            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
            ->where('invigilation_catergories.id', '=',  $center->category_id)
            ->where('invigilation_candidates.range_start', '<=', $totalcandidates)
            ->where('invigilation_candidates.range_end', '>=', $totalcandidates)
            ->get();

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
            ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
            ->leftJoin('invigilator_profile', 'invigilator_profile.invigilation_role_id', '=', 'invigilation_roles.id')
            ->groupBy('invigilation_roles.invigilation_type_id')
            ->where('center_no', '=', $center_no)
            ->where('session',  $session->session)
            ->where('financial_year',  $session->financial_year)
            ->where('progress_status_id', '!=', $status->id)
            ->get();

        $invigilator_experiences = InvigilatorExperience::get();
        $invigilation_status = InvigilationStatus::get();

        $acceptedNumber = DB::table('invigilator_profile')
            ->select(
                DB::raw("COUNT( progress_status_id) as acceptedNumber"),
            )
            ->where('progress_status_id', '=', 2)
            ->where('center_no', '=', $center_no)
            ->where('session',  $session->session)
            ->where('financial_year',  $session->financial_year)
            ->count();
        $pendingNumber = DB::table('invigilator_profile')
            ->select(
                DB::raw("COUNT( progress_status_id) as pendingNumber"),
            )
            ->where('progress_status_id', '=', 1)
            ->where('center_no', '=', $center_no)
            ->where('session',  $session->session)
            ->where('financial_year',  $session->financial_year)
            ->count();
        $declinedNumber = DB::table('invigilator_profile')
            ->select(
                DB::raw("COUNT( progress_status_id) as declinedNumber"),
            )
            ->where('progress_status_id', '=', 3)
            ->where('session',  $session->session)
            ->where('financial_year',  $session->financial_year)
            ->where('center_no', '=', $center_no)
            ->count();
        return view('school.invigilator.index', compact('declinedNumber', 'pendingNumber', 'acceptedNumber', 'invigilation_types', 'totalcandidates', 'list_rules', 'invigilator_experiences', 'invigilation_status'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {



        $center = Center::with('subjects')->where('center_no', '=', auth()->user()->center_no)->first();
        $centerSessions = json_decode($center->sessions, true);

        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();
        $center_no = $center->center_no;


        $validator = Validator::make($request->all(), [
            'invigilation_role_id' => ['required', Rule::notIn($this->validate_invigilators($center_no, $center->category_id))],
            'national_id' => ['required', 'regex:/^(\d{11}|\d{12}|\d{13})$/', Rule::unique('invigilator_profile')->where(function ($q) use ($session) {
                return $q->where('session', $session->session)
                    ->where('financial_year',  $session->financial_year);
            })],
            'surname' => 'required|string',
            'other_names' => 'required|string',
            'email' => ['required', 'string', 'email', Rule::unique('invigilator_profile')->where(function ($q) use ($session) {
                return $q->where('session', $session->session)
                    ->where('financial_year',  $session->financial_year);
            })],
            'phone_number' => 'required',
            'experience_id' => 'required|string',
            'principal_declare' =>  'required|string',
        ], ['invigilation_role_id.not_in' => 'Reached the limit']);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }






        $token = md5(uniqid());

        $status = InvigilationStatus::where('status', '=', 1)->orderBy('order_status', 'ASC')->first();


        $invigilation = InvigilatorProfile::create([
            'invigilation_role_id' => $request->invigilation_role_id,
            'national_id' => $request->national_id,
            'surname' => $request->surname,
            'other_names' => $request->other_names,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'experience_id' => $request->experience_id,
            'accessibility' => $request->has('accessibility') ? 1 : 0,
            'integrity' => $request->has('integrity') ? 1 : 0,
            'workshop' => $request->has('workshop') ? 1 : 0,
            'principal_declare' => $request->principal_declare,
            'token' => $token,
            'center_no' => $center_no,
            'progress_status_id' => $status->id,
            'session' => $session->session,
            'financial_year' => $session->financial_year,
        ]);

        // send email
        $url = route('applications.index', $token);
        $declined = route('applications.index', ['token' => $token, 'declined' => 1]);
        $mailheader = '\assets\images\mailheader.jpg';
        $data = ['center_no' => $center_no, 'other_names' => $request->other_names, 'surname' => $request->surname, 'url' => $url, 'declined' => $declined, 'center_name' => $center->center_name, 'mailheader' => $mailheader];
        Mail::to($request->email)->queue(new InvigilatorMail($data));
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
        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $validator = Validator::make($request->all(), [
            'invigilation_role_id' => ['required', Rule::notIn($this->validate_invigilators($center_no, $center->category_id, $id))],
            'national_id' => ['required', 'regex:/^(\d{11}|\d{12}|\d{13})$/', 'unique:invigilator_profile,national_id,' . $id],
            'surname' => 'required|string',
            'other_names' => 'required|string',
            'email' => 'required|string|email|unique:invigilator_profile,email,' . $id,
            'phone_number' => 'required|string',
            'experience_id' => 'required|string',
            'principal_declare' =>  'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' =>  $validator->errors()]);
        } else {
            $token = md5(uniqid());
            $invigilation = InvigilatorProfile::find($id);
            $invigilation->invigilation_role_id = $request->invigilation_role_id;
            $invigilation->national_id = $request->national_id;
            $invigilation->surname = $request->surname;
            $invigilation->other_names = $request->other_names;
            $invigilation->email = $request->email;
            $invigilation->phone_number = $request->phone_number;
            $invigilation->experience_id = $request->experience_id;
            $invigilation->accessibility = $request->has('accessibility') ? 1 : 0;
            $invigilation->integrity = $request->has('integrity') ? 1 : 0;
            $invigilation->workshop = $request->has('workshop') ? 1 : 0;
            $invigilation->principal_declare = $request->principal_declare;
            if ($request->has('resend_token')) {
                $invigilation->token = $token;
            }

            $invigilation->save();
            if ($request->has('resend_token')) {

                // send email

                $declined = route('applications.index', ['token' => $token, 'declined' => 1]);
                $url = route('applications.index', $token);
                $mailheader = '\assets\images\mailheader.jpg';
                $data = ['center_no' => $center_no, 'other_names' => $request->other_names, 'surname' => $request->surname, 'url' => $url, 'declined' => $declined, 'center_name' => $center->center_name, 'mailheader' => $mailheader];
                Mail::to($request->email)->queue(new InvigilatorMail($data));
            }
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


    private function validate_invigilators($center_no, $type, $id = null)
    {
        if (empty($center_no) || empty($type)) {
            return  [];
        }
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)
            ->where('is_active', '=', 1)
            ->first();

        $totalcandidates = Payment::highestSubject($center_no, $center->level,  $session->session, $session->financial_year);

        $status = InvigilationStatus::where('status', '=', 0)
            ->orderBy('order_status', 'ASC')->first();



        $invigilation_types = DB::table('invigilation_candidates')
            ->select(
                'invigilation_candidates.range_start',
                'invigilation_candidates.range_end',
                'invigilation_types.name',
                DB::raw("invigilation_catergories.name as catergory_name"),
                'invigilation_roles.id',
                'invigilation_roles.invigilator_number'
            )
            ->leftJoin('invigilation_roles', 'invigilation_roles.invigilation_candidate_id', '=', 'invigilation_candidates.id')
            ->join('invigilation_types', 'invigilation_types.id', '=', 'invigilation_roles.invigilation_type_id')
            ->join('invigilation_catergories', 'invigilation_catergories.id', '=', 'invigilation_types.invigilation_catergories_id')
            ->where('invigilation_catergories.id', '=', $type)
            ->where('invigilation_candidates.range_start', '<=', $totalcandidates)
            ->where('invigilation_candidates.range_end', '>=', $totalcandidates)
            ->get();




        $invigilation_roles = DB::table('invigilator_profile')
            ->select('invigilation_role_id', DB::raw("COUNT(invigilation_role_id) as invigilation_number"))
            ->where('financial_year', '=', $financial_year)
            ->groupBy('center_no', 'invigilation_role_id');

        if (!empty($id)) {
            $id_role = InvigilatorProfile::find($id);
            $invigilation_roles = $invigilation_roles->where('invigilation_role_id', '<>', $id_role->invigilation_role_id);
        }
        $invigilation_roles = $invigilation_roles->where('center_no', '=', $center_no)
            ->where('progress_status_id', '!=', $status->id)
            ->get();

        $rules = [];
        foreach ($invigilation_types as    $invigilation_type) {
            foreach ($invigilation_roles as $invigilation_role) {
                if ($invigilation_type->id == $invigilation_role->invigilation_role_id && $invigilation_type->invigilator_number <= $invigilation_role->invigilation_number) {
                    array_push($rules, $invigilation_role->invigilation_role_id);
                }
            }
        }
        return    $rules;
    }
}
