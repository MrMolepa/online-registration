<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Candidate;
use App\Models\CandidateArrangement;
use App\Models\CandidateUser;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Documents;
use App\Models\DocumentUserPermissions;
use App\Models\Guardian;
use App\Models\GuardianType;
use App\Models\Invoice;
use App\Models\Level;
use App\Models\OptionHeader;
use App\Models\Session;
use App\Models\SpecialNeed;
use App\Models\Subject;
use App\Models\SubjectCandidate;
use App\Rules\CheckDupsSubject;
use App\Rules\Extended;
use App\Rules\SubjectsGrouping;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DocumentPermissionControllerMulti extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
        $sponsors = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->distinct()
            ->orderBy('sponser')
            ->get();
        $types = DB::table('center_candidate')->select(
            [
                'type'
            ],
        )->distinct()
            ->orderBy('type')
            ->get();
        $centers  = Center::get();
        if ($request->ajax()) {
            $documents = Documents::with('categories', 'users.document_user');
            return DataTables::of($documents)
                ->editColumn('document_name', function ($row) {
                    return  $row->name;
                })
                ->editColumn('created_date', function ($row) {
                    return  date('d-m-Y', strtotime($row->created_date));
                })
                ->editColumn('checkbox', function ($row) {
                    return   "<input type='checkbox' class='documents-select' name='documents[]' value='$row->id'>";
                })
                ->rawColumns(['checkbox', 'document_name'])
                ->make();
        }
        $levels = Level::get();
        $sessions = Session::where('financial_year', $years[0])->get();
        $specialNeeds = SpecialNeed::get();
        $guardian_types =  GuardianType::get();
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        return view('admin.documents.documents.multi', compact('centers', 'sponsors', 'years', 'levels', 'types', 'sessions', 'districts', 'specialNeeds', 'guardian_types'));
    }

    public function centersAccounts(Request  $request)
    {


        $level = $request->level;
        $session = $request->session;
        $center = $request->center;
        $filter = $request->filter;
        $year = $request->year;


        $subject = $request->subject;
        $output = "";

        $subjects = DB::table('subjects')
            ->select(
                [
                    'subjects.subject_code',
                    'subjects.subject_name',
                    'subjects.short_name',
                    'levels.level',
                ],
            )
            ->join('levels', 'levels.id', '=', 'subjects.level');

        $candidates_per_centers = DB::table('center_candidate')
            ->select(
                [
                    'users.id',
                    'center_candidate.center_no',
                    'centers.center_name',
                    'centers.district',
                    DB::raw("count(DISTINCT center_candidate.candidate_no ) as candidates"),
                    DB::raw("group_concat(DISTINCT concat(center_candidate.level)
                order by center_candidate.level separator ',') as levels"),
                    DB::raw("group_concat(DISTINCT concat(center_candidate.session)
                order by center_candidate.session separator ',') as sessions"),
                    DB::raw("group_concat(DISTINCT concat(center_candidate.sponser)
                order by center_candidate.sponser separator ',') as sponsors"),
                ],
            )
             ->join('candidate_subject', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no')
            ->join('users', 'centers.center_no', '=', 'users.center_no');

        if (!is_null($subject)) {
            $candidates_per_centers = $candidates_per_centers->where('candidate_subject.subject_code', '=', $subject);
            $subjects = $subjects->where('subjects.subject_code', '=',  $subject);
        }
        if (!is_null($level)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.level', '=', $level);
            $subjects = $subjects->where('levels.level', '=', $level);
        }
        if (!is_null($session)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.session', '=', $session);
        }
        if (!is_null($center)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.center_no', '=', $center);
        }
        if (!is_null($year)) {
            $candidates_per_centers = $candidates_per_centers->where('center_candidate.financial_year', '=',  $year);
        }

        $candidates_per_centers = $candidates_per_centers->groupBy('center_candidate.center_no')
            ->take($filter)
            ->get();
        $subjects = $subjects->get();

        $totalCandidates = 0;

        if (count($candidates_per_centers) > 0) {
            $output = "<table class='table table-condensed table-striped' id='users-permissions-table'>
            <thead>
                <tr>
                    <th><input type='checkbox' class='users-permissions-select-all' name='users_permissions-selected' value='1'></th>
                    <th>Center No</th>
                    <th>Center Name</th>
                    <th>District</th>
                    <th>Level(s)</th>
                    <th>Session(s)</th>
                    <th>Sponsor(s)</th>
                    <th>Registered Candidates</th>
                </tr>
            </thead>
            <tbody>";
            foreach ($candidates_per_centers as $center) {
                $output .= "<tr>
                         <td><input type='checkbox' class='users-permissions-select' name='users_permissions[]' value='$center->id'></td>
                        <td>$center->center_no </td>
                        <td>$center->center_name </td>
                        <td>$center->district </td>
                        <td> $center->levels </td>
                        <td> $center->sessions </td>
                        <td> $center->sponsors </td>
                        <td> $center->candidates </td>
                    </tr>";
                $totalCandidates = $totalCandidates + $center->candidates;
            }

            $output  .= "<th colspan=6 class='heading'>Total Candiates</th>
            <th> $totalCandidates</th>";




            $output  .= " </tbody>
        </table>";
        } else {
            $output =  '<div>
                            No Candidates
                        </div>';
        }

        return response()->json(['cendidate_per_center' => $output, 'centers' => $candidates_per_centers, 'subjects' => $subjects, 'filet' => $request->all()]);
    }


    public function multipleDocumentsToUser(Request  $request)
    {
        $validator = Validator::make($request->all(), [
            'document_user_Permission' => ['required', 'array'],
            'users_permissions' => ['required', 'array'],
            'documents' => ['required', 'array'],
            'document_user_Permission.start_date' => ['required_with:document_user_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'before:document_user_Permission.end_date'],
            'document_user_Permission.end_date' => ['required_with:document_user_Permission.is_time_bound', 'nullable', 'date_format:Y-m-d\TH:i', 'after:document_user_Permission.start_date'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $documentUserPermissions = (object)$request->document_user_Permission;
            foreach ($request->documents as $document) {
                $users = DB::table('document_users')
                    ->whereIn('document_user_id', $request->users_permissions)
                    ->get()->pluck('id')->toArray();

                $document_user_permissions = DB::table('document_user_permissions')
                    ->whereIn('user_id',    $users)
                    ->where('document_id', $document)
                    ->get()->pluck('user_id')->toArray();
                $users = array_diff($users, $document_user_permissions);
                $startDate = '';
                $endDate = '';
                $is_allow_download = isset($documentUserPermissions->is_allow_download) ? $documentUserPermissions->is_allow_download : 0;
                $is_time_bound = isset($documentUserPermissions->is_time_bound) ? $documentUserPermissions->is_time_bound : 0;
                if (isset($documentUserPermissions->is_time_bound)) {
                    $startdate = date('Y-m-d\TH:i', strtotime($documentUserPermissions->start_date));
                    $enddate = date('Y-m-d\TH:i', strtotime($documentUserPermissions->end_date));
                    $startDate =  $startdate;
                    $endDate =  $enddate;
                }
                //Users

                foreach ($users as  $userid) {
                    DocumentUserPermissions::create([
                        'document_id' => $document,
                        'is_allow_download' => $is_allow_download,
                        'is_time_bound' => $is_time_bound,
                        'user_id' => $userid,
                        'start_date' => $startDate ?? '',
                        'end_date' => $endDate ?? '',
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Successfully saved the records']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                $e->getMessage()
            ]);
        }
    }
}
