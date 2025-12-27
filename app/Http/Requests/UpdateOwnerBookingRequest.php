<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnerBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $booking = $this->route('booking');
        $notOkStatus = in_array($booking->status, [
            'rejected',
            'cancelled',
            'finished',
            'started',
            'failed',
        ]);

        // return $booking && $this->user()->id === $booking->user_id;
        return $booking && $this->user()->id === $booking->apartment->user_id && !$notOkStatus;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:approved,rejected'],
        ];
    }
}
