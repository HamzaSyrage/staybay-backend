<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
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
        //    $table->id();
        //     $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
        //     $table->foreignIdFor(Governorate::class)->constrained()->onDelete('cascade');
        //     $table->foreignIdFor(City::class)->constrained()->onDelete('cascade');
        //     $table->string('title');
        //     $table->text('description');
        //     $table->float('price');
        //     $table->float('rating')->default(0);
        //     $table->integer('rooms');
        //     $table->integer('bedrooms');
        //     $table->integer('size');
        //     $table->boolean('has_pool');
        //     $table->boolean('has_wifi');

        return [
            'governorate_id' => ['required', 'exists:governorates,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'title' => ['required'],
            'description' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
            'rooms' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['required', 'numeric', 'min:0'],
            'size' => ['required', 'numeric', 'min:0'],
            'has_pool' => ['required', 'boolean'],
            'has_wifi' => ['required', 'boolean'],
        ];
    }
}
