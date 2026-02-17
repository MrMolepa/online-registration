<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FunWalkRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'fun_walk_id' => 'required|exists:fun_walks,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits:8',
            'ticket_number' => 'nullable|string|max:255|unique:fun_walk_registrations,ticket_number',
            'qr_path' => 'nullable|string|max:255',
        ];

        // If updating, allow the same ticket number for the current registration
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $registrationId = $this->route('funWalkRegistration') 
                ? $this->route('funWalkRegistration')->id 
                : $this->input('funWalkRegistration_id');
                
            if ($registrationId) {
                $rules['ticket_number'] = 'nullable|string|max:255|unique:fun_walk_registrations,ticket_number,' . $registrationId;
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'fun_walk_id.required' => 'Please select a fun walk event',
            'fun_walk_id.exists' => 'Selected fun walk does not exist',
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'date_of_birth.required' => 'Date of birth is required',
            'date_of_birth.before' => 'Date of birth must be in the past',
            'gender.required' => 'Gender is required',
            'gender.in' => 'Please select a valid gender',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'phone.required' => 'Phone number is required',
            'phone.digits' => 'Phone number must be exactly 8 digits',
            'ticket_number.unique' => 'This ticket number already exists',
        ];
    }
}