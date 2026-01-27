<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SubjectValidator;
use App\Models\Center;
use App\Models\Level;
use Illuminate\Http\Request;

class CandidateValidationController extends Controller
{
    protected $subjectValidator;

    public function __construct(SubjectValidator $subjectValidator)
    {
        $this->subjectValidator = $subjectValidator;
    }

    /**
     * Validate subject selection via AJAX
     */
    public function validateSubjects(Request $request)
    {
        $subjectCodes = $request->subject_codes ?? [];
        $centerNo = $request->center_no;
        $type = $request->type ?? 1;
        $financialYear = $request->financial_year ?? $this->getCurrentFinancialYear();

        // Get center and level
        $center = Center::where('center_no', $centerNo)->first();

        if (!$center) {
            return response()->json([
                'valid' => false,
                'errors' => ['Center not found'],
                'warnings' => []
            ]);
        }

        $level = Level::where('level', $center->level)->first();

        if (!$level) {
            return response()->json([
                'valid' => false,
                'errors' => ['Level not found'],
                'warnings' => []
            ]);
        }

        // Validate subjects
        $result = $this->subjectValidator->validate(
            $subjectCodes,
            $level->id,
            $financialYear,
            $type,
            $centerNo
        );

        return response()->json($result);
    }

    /**
     * Get current financial year
     */
    private function getCurrentFinancialYear()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        if ($currentMonth <= 3) {
            return ($currentYear - 1) . '-' . $currentYear;
        } else {
            return $currentYear . '-' . ($currentYear + 1);
        }
    }
}