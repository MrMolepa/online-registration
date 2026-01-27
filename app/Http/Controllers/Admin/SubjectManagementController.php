<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectManagementController extends Controller
{
    /**
     * Display the unified subject management page
     */
    public function index(Request $request)
    {
        // Get data needed for both tabs
        $levels = Level::where('is_active', true)->get();
        $subjects = Subject::all();

        return view('admin.subject-management.index', compact('levels', 'subjects'));
    }
}