<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\GuardianType;
use App\Models\Level;
use App\Models\Session;
use App\Models\SpecialNeed;
use Illuminate\Http\Request;
use App\Models\Subject;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $center = Center::where('center_no', '=', auth()->user()->center_no)->first();
        $levels = Level::where('is_active', '=', 1)->get();
        $sessions = Session::where('is_active', '=', 1)->where('session', '=', "November")->get();
        $subjects =  $center->subjects;
        $specialNeeds = SpecialNeed::get();
        $guardian_types =  GuardianType::get();
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        return view("school.dashboard", compact('center','levels', 'subjects', 'sessions', 'districts', 'specialNeeds', 'guardian_types'));
    }
}
