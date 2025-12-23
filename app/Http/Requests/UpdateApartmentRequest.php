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

            'city_id' => ['sometimes', 'exists:cities,id'],
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'rooms' => ['sometimes', 'integer', 'min:0'],
            'bedrooms' => ['sometimes', 'integer', 'min:0'],
            'size' => ['sometimes', 'integer', 'min:0'],
            'has_pool' => ['sometimes', 'boolean'],
            'has_wifi' => ['sometimes', 'boolean'],

            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp'],
        ];
    }
}
