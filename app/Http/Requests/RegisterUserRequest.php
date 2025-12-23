<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class RegisterUserRequest extends FormRequest
{
    public function rules()
    {
        $tenYearsAgo = Carbon::now()->subYears(10)->format('Y-m-d');
        $longTimeAgo = Carbon::now()->subYears(120)->format('Y-m-d');

        return [

            'phone' => ['required','numeric','digits_between:10,11', 'unique:users,phone'],
            'first_name' => ['required', 'min:2', 'max:50'],
            'last_name' => ['required', 'min:2', 'max:50'],

            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],

            'id_card' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],

            'birth_date' => ['required', 'date' ,'before:'.$tenYearsAgo,'after:'.$longTimeAgo],

            'password' => ['required', 'confirmed', 'min:8', 'max:255'],

        ];
    }

    public function authorize()
    {
        return true;
    }
}
