<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function rules()
    {
        return [

            'phone' => ['required', 'unique:users,phone'],
            'first_name' => ['required', 'min:2', 'max:50'],
            'last_name' => ['required', 'min:2', 'max:50'],

            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],

            'id_card' => ['required', 'image', 'mimes:jpg,jpeg,png'],

            'birth_date' => ['required', 'date'],

            'password' => ['required', 'confirmed', 'min:8', 'max:255'],

        ];
    }

    public function authorize()
    {
        return true;
    }
}
