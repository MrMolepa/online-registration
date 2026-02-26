<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'service' => 'required|exists:one_time_services,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_identity' => 'required|digits:12',
            'phone' => 'required|string|max:8',
            'email' => 'required|email',
            'serviceItem' => 'required|exists:one_time_services_item,id',
            'total_sale_price' => 'required|numeric|min:0',
        ];

        // Conditional rules based on payment method
        if ($this->input('payment') === 'VclMpesa') {
            $rules['mpesa_mobile'] = 'required|digits:8';
        } elseif ($this->input('payment') === 'EcoCash') {
            $rules['ecocash_mobile'] = 'required|digits:8';
        } elseif ($this->input('payment') === 'BankDeposit') {
            $rules['deposit_proof'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
            $rules['deposit_date'] = 'required|date';
            $rules['deposit_reference'] = 'required|string';
        }

        return $rules;
    }
}
