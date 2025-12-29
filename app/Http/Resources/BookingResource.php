<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $payments = $this->relationLoaded('payments') ? $this->payments : collect();


        $netPaid = $payments->whereIn('status', ['hold', 'completed'])->sum('amount');

        $isFullyPaid = $netPaid >= $this->total_price;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'apartment_id' => $this->apartment_id,
            'apartment' => new ApartmentResource($this->whenLoaded('apartment')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'total_paid' => $netPaid,
            'rating' => $this->rating ?? 0,
            'rated_at' => $this->rated_at,
            'user_can_rate' => $this->status === 'finished',
            'is_paid' => $isFullyPaid,

            'can_user_pay' => !$isFullyPaid && $this->status === 'approved',
            'can_user_edit' => !in_array($this->status, [
                'rejected',
                'cancelled',
                'finished',
                'started',
                'failed',
            ]),
            'can_owner_edit' => !in_array($this->status, [
                'approved',
                'rejected',
                'cancelled',
                'finished',
                'started',
                'failed',
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
