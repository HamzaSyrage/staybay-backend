<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payments = $this->whenLoaded('payments') ?? $this->payments;

        $holdAmount = $payments->sum('amount') ?? 0;
        $completedAmount = $payments->sum('amount') ?? 0;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'apartment_id' => $this->apartment_id,
            // 'apartment' => new ApartmentResource($this->whenLoaded('apartment')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'rating' => $this->rating ?? 0,
            'rated_at' => $this->rated_at,
            'user_can_rate' => $this->status === 'finished',
            'is_paid' => $completedAmount >= $this->total_price,
            'can_user_pay' => ($holdAmount + $completedAmount) < $this->total_price && $this->status === 'approved',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}
