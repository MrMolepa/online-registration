<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Extended implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */



    public function __construct()
    {
        
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
        $extentedSubject = ['178', '181'];
     
        if (!in_array($value['subject_code'], $extentedSubject)  && $value['subject_option'] == 'B') {
            
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The option B is only for subject code 0178 or 0181';
    }
}
