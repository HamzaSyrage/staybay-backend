<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'avatar' => ['nullable'],
            'id_card' => ['required'],
            'birth_date' => ['required', 'date'],
            'password' => ['required'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
