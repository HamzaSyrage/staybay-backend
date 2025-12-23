<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApartmentRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //user id from auth
            // 'governorate_id' => ['required', 'exists:governorates'],
            'city_id' => ['exists:cities'],
            'title' => [''],
            'description' => [''],
            'price' => ['numeric', 'min:0'],
            'rooms' => ['numeric', 'min:0'],
            'bedrooms' => ['numeric', 'min:0'],
            'size' => ['numeric', 'min:0'],
            'has_pool' => ['boolean'],
            'has_wifi' => ['boolean'],

        ];
    }
}
