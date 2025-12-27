<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $booking = $this->route('booking');
        $notOkStatus = in_array($booking->status, [
            'rejected', //? should we allow user to edit if rejected?
            'cancelled',
            'finished',
            'started',
            'failed',
        ]);
        return $booking && $this->user()->id === $booking->user_id && !$notOkStatus;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['sometimes', 'date', 'before:end_date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'status' => ['sometimes', 'in:cancelled'],
        ];
    }
}
