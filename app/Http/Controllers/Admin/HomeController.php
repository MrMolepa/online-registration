<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenterCandidate;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    //

    public function index(Request $request)
    {

        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
        $sessions =  CenterCandidate::select('session')
        ->where('financial_year', '=',    $years[0])
            ->distinct()
            ->get()->pluck('session');
       $levels =  CenterCandidate::select('level')
            ->where('financial_year', '=',    $years[0])
                ->distinct()
                ->get()->pluck('level');

        $registered_schools = DB::table('center_candidate')
            ->select(
                [
                    DB::raw("count(DISTINCT center_no ) as schools")
                ],
            )
            ->where('financial_year', '=',    $years[0])
            ->first();

        $number_of_candidates = DB::table('center_candidate')
            ->select(
                [DB::raw("count(DISTINCT candidate_no ) as number_of_candidates")],
            )
            ->where('financial_year', '=',    $years[0])
            ->first();
        $number_of_private_candidates = DB::table('center_candidate')
            ->select(
                [DB::raw("count(DISTINCT candidate_no ) as number_of_candidates")],
            )->whereIn('type', [2, 3])
            ->where('financial_year', '=',    $years[0])
            ->first();

        $number_of_school_candidates = DB::table('center_candidate')
            ->select(
                [DB::raw("count(DISTINCT candidate_no ) as number_of_candidates")],
            )->whereIn('type', [1])
            ->where('financial_year', '=',    $years[0])
            ->first();



        $subjects = Subject::get();
        if ($request->ajax()) {
            $subjects = DB::table('total_subjects_per_year')
                ->select('total_subjects_per_year.*', DB::raw('SUM(total) AS total'))
                ->where('session', $request->input('session'))
                ->where('level','=', $request->input('level'))
                ->where('financial_year', '=', $request->input('year'))
                ->groupBy('subject_code', 'subject_option', 'session', 'financial_year')
                ->get();


             $candidates = DB::table('total_candidate_per_type')
                ->select(
                DB::raw('SUM(total) AS total'),
                DB::raw('SUM(private_candidate) AS private_candidate'),
                DB::raw('SUM(school_candidate) AS school_candidate'))
                ->where('session', $request->input('session'))
                ->where('level','=', $request->input('level'))
                ->where('financial_year', '=', $request->input('year'))
                ->groupBy('session', 'financial_year')
                ->first();

                $schools = DB::table('center_candidate')
                ->select(
                    [
                        DB::raw("count(DISTINCT center_no ) as total")
                    ],
                )
                ->where('level','=', $request->input('level'))
                ->where('financial_year', '=',    $request->input('year'))
                ->where('session', '=',    $request->input('session'))
                ->first();

            return response()->json(['subjects'=>$subjects,'candidates'=> $candidates,'schools'=> $schools ]);
        }
        return view('admin.dashboard', compact('registered_schools', 'number_of_candidates', 'number_of_private_candidates', 'number_of_school_candidates', 'years', 'sessions', 'subjects','levels'));
    }
    public function registeredSubjects()
    {

        $registered_subjects = DB::table('registered_subjects')
            ->first();


        return response()->json($registered_subjects);
    }



}
