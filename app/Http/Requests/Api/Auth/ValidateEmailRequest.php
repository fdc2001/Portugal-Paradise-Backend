<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ValidateEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email'
        ];
    }
}
