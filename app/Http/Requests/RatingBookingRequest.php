<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // $booking = $this->route('booking');
        // return $booking && $this->user()->id === $booking->user_id && $booking->status === 'finished' && $booking->rated_at === null;
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
            'rating' => ['required', 'numeric', 'between:0,5'],
        ];
    }
}
