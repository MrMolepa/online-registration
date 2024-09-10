<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\CenterCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{


    public function index(Request $request)
    {
        $sponsor = auth()->user()->sponsor;
        $level = auth()->user()->level;



        $sponsor_year =  CenterCandidate::select('financial_year', 'level', 'session')
            ->where('level', '=',    $level)
            ->where('sponser', '=',     $sponsor)
            ->orderBy('financial_year', 'DESC')
            ->distinct()
            ->first();
        if ($request->ajax()) {
            $districts = DB::table('centers')
                ->select('districts.district_code', 'districts.district_name', DB::raw("count(center_candidate.candidate_no) as total"))
                ->join('center_candidate', 'center_candidate.center_no', '=', 'centers.center_no')
                ->join('districts', 'districts.district_code', '=', 'centers.district_code')
                ->where('center_candidate.session','=','November')
                ->where('center_candidate.financial_year','=',$sponsor_year->financial_year)
                ->where('center_candidate.sponser','=',   $sponsor)
                ->where('center_candidate.level','=',   $level)
                ->groupBy('districts.district_code')
                ->orderBy('district_code', 'asc')
                ->get();
            return response()->json(['districts' => $districts]);
        }

        $registered_schools = DB::table('center_candidate')
            ->select(
                [
                    DB::raw("count(DISTINCT center_no ) as schools")
                ],
            )
            ->where('financial_year', '=',  $sponsor_year->financial_year)
            ->where('level', '=',    $level)
            ->where('sponser', '=',     $sponsor)
            ->first();

        $number_of_candidates = DB::table('center_candidate')
            ->select(
                [DB::raw("count(DISTINCT candidate_no ) as number_of_candidates")],
            )
            ->where('financial_year', '=',  $sponsor_year->financial_year)
            ->where('level', '=',    $level)
            ->where('sponser', '=',     $sponsor)
            ->first();

        $approved = DB::table('request_action')
            ->select([DB::raw("count(request_action.id) as status")])
            ->join('requests', 'requests.id', '=', 'request_action.request_id')
            ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
            ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
            ->join('actions', 'actions.id', '=', 'request_action.action_id')
            ->join('processes', 'processes.id', '=', 'actions.process')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('requests.request_data', '=', CenterCandidate::class)
            ->where('processes.process_key', '=',  $sponsor)
            ->where('request_action.is_complete', '=', 1)
            ->where('action_types.name', '=', 'Approve')
            ->groupBy('request_action.request_id')
            ->having(DB::raw("count(request_action.request_id)"), '>', 1)
            ->get()->count();
        $declined = DB::table('request_action')
            ->select([DB::raw("count(DISTINCT request_action.id) as status")])
            ->join('requests', 'requests.id', '=', 'request_action.request_id')
            ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
            ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
            ->join('actions', 'actions.id', '=', 'request_action.action_id')
            ->join('processes', 'processes.id', '=', 'actions.process')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('requests.request_data', '=', CenterCandidate::class)
            ->where('request_action.is_active', '=', 0)
            ->where('processes.process_key', '=',  $sponsor)
            ->where('request_action.is_complete', '=', 1)
            ->where('action_types.name', '=', 'Decline')
            ->first();

        return view('sponsor.home', compact('registered_schools', 'number_of_candidates', 'declined', 'approved'));
    }
}
