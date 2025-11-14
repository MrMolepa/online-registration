<?php

namespace App\Rules;

use App\Models\Center;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Contracts\Validation\Rule;

class SubjectsGrouping implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $center_no ;
    public function __construct($center_no)
    {
        $this->center_no=$center_no;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {


        $center = Center::with('subjects')->where('center_no', '=', $this->center_no)->first();

        $level = $center->level ;

        switch ($level) {
            case 'G7ELT':
                $center_subjects= $center->subjects->pluck('subject_code')->toArray();
                $subjects = $this->changeToSubjects($value);
                if (count($subjects) > 0) {
                    $compulsorySubjects = array_map('intval',  $center_subjects);
                    $checkIntersetCompulsory = array_intersect($subjects, $compulsorySubjects);
                    if (in_array($value[0]['type'], [1])) {
                        if (
                            count($checkIntersetCompulsory) == count( $center_subjects) &&
                            in_array($value[0]['type'], [1])
                        ) {
                            return true;
                        }
                        return false;
                     } else {
                        return false;
                    }
                } else {
                    return false;
                }
                break;
            case 'LGCSE':
                $subjects = $this->changeToSubjects($value);
                if (count($subjects) > 0) {
                    $compulsorySubjects = array_map('intval', Subject::whereIn('subject_code', [175, 176, 178])->pluck('subject_code')->toArray());
                    $sciencesSubjects = array_map('intval', Subject::whereIn('subject_code', [180, 181, 197, 198])->pluck('subject_code')->toArray());
                    $socialScienceSubjects = array_map('intval', Subject::whereIn('subject_code', [185, 186, 184, 183, 182, 177])->pluck('subject_code')->toArray());
                    $praticalSubjects = array_map('intval', Subject::whereIn('subject_code', [179, 191, 192, 190, 194, 189])->pluck('subject_code')->toArray());
                    $creativeSubjects = array_map('intval', Subject::whereIn('subject_code', [187, 188])->pluck('subject_code')->toArray());
                    $lbseSubjects = array_map('intval', Subject::whereIn('subject_code', [2030])->pluck('subject_code')->toArray());

                    $checkIntersetCompulsory = array_intersect($subjects, $compulsorySubjects);
                    $checkIntersetSciences = array_intersect($subjects,  $sciencesSubjects);
                    $checkIntersetSocialScience = array_intersect($subjects, $socialScienceSubjects);
                    $checkIntersetPratical = array_intersect($subjects, $praticalSubjects);
                    $checkIntersetCreative = array_intersect($subjects, $creativeSubjects);
                    $CreativeAndPratical = array_merge($checkIntersetCreative,  $checkIntersetPratical);
                    $checkIntersetlbse = array_intersect($subjects,  $lbseSubjects);
                    if (in_array($value[0]['type'], [3])) {
                        if (
                            in_array($value[0]['type'], [3]) &&
                            count($checkIntersetPratical) == 0 &&
                            count($checkIntersetlbse) == 0
                        ) {
                            return true;
                        }
                        return false;
                    } else if (in_array($value[0]['type'], [2])) {
                        if (
                            count($checkIntersetCompulsory) == 3 &&
                            in_array($value[0]['type'], [2]) &&
                            count($checkIntersetSciences) > 0 &&
                            count($checkIntersetPratical) == 0 &&
                            count($checkIntersetCreative) > 0 &&
                            count($checkIntersetlbse) == 0 &&
                            count($checkIntersetSocialScience) > 0
                        ) {
                            return true;
                        }
                        return false;
                    } else if (in_array($value[0]['type'], [1])) {
                        if (
                            count($checkIntersetCompulsory) == 3 &&
                            count($checkIntersetSciences) > 0 &&
                            count($checkIntersetlbse) > 0 &&
                            count($CreativeAndPratical) > 0 &&
                            count($checkIntersetSocialScience) > 0
                        ) {
                            return true;
                        }
                        return false;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                break;
            default:
                break;
        }


    }

    private function changeToSubjects($subjects)
    {
        $newArray = array();
        foreach ($subjects as $key => $subject) {
            array_push($newArray, $subject['subject_code']);
        }
        return $newArray;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $center = Center::with('subjects')->where('center_no', '=',  $this->center_no)->first();
        $level =  $center->level;
        return "The invalid  $level Examinations Subjects  Grouping";
    }
}
