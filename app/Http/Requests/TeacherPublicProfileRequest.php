<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherPublicProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // A: mínims
            'name'  => ['required', 'string'],
            'email' => ['required', 'email'],

            // B: RGPD
            'consent_rgpd' => ['accepted'],

            // C: financials
            '// needs_payment' => ['nullable', 'boolean'],

           /*  'first_name'  => [Rule::requiredIf($this->needs_payment), 'string'],
            'last_name_1' => [Rule::requiredIf($this->needs_payment), 'string'],
            'last_name_2' => ['nullable', 'string'], */

            /* 'dni' => [
                Rule::requiredIf($this->needs_payment),
                'regex:/^([0-9]{8}[A-Z]|[XYZ][0-9]{7}[A-Z])$/'
            ],

            'postal_code' => [
                Rule::requiredIf($this->needs_payment),
                'regex:/^[0-9]{5}$/'
            ],

            'iban' => [
                Rule::requiredIf($this->needs_payment),
                'iban'
            ],

            'bank_holder' => [
                Rule::requiredIf($this->needs_payment),
                'string'
            ], */
        ];
    }
}
