<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ArrayAtleastOneRequired implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        foreach ($value as $arrayElement) {
            if (null !== $arrayElement) {
                if (!is_numeric($arrayElement)) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Atleast one input is required';
    }
}
