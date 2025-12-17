<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginDashboardRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => ['required|numeric|digits_between:10,11'],
            'password' => ['required'],
        ];
    }

    public function authorize()
    {
        return true;
    }
    public function credentials(): array
    {
        return [
            'phone' => $this->phone,
            'password' => $this->password,
            'is_admin' => true,
        ];
    }
}
