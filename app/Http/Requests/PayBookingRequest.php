<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // $booking = $this->route('booking');
        // return $booking && $this->user()->id === $booking->user_id && $booking->status === 'approved' && $booking->paid_at === null;
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

        ];
    }
}
