<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginDashboardRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => ['required'],
            'password' => ['required'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
