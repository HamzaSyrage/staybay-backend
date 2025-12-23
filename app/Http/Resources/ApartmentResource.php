<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'owner_id' => $this->user_id,
            "owner" => new UserResource($this->whenLoaded('user')),
            //// i will make them a resource later but for now they are hard coded
            // 'governorate_id' => $this->governorate_id,
            // 'city_id' => $this->city_id,
            // 'governorate' => [
            //     'id' => $this->governorate->id,
            //     'name' => $this->governorate->name
            // ],
            // 'city' => [
            //     'id' => $this->city->id,
            //     'name' => $this->city->name
            // ],
            'governorate' => new GovernorateResource($this->whenLoaded('governorate')),
            'city' => new CityResource($this->whenLoaded('city')),
            'is_favorite' => $this->when(
                auth('sanctum')->check(),
                fn() => $this->favoriteUsers()
                    ->where('user_id', auth('sanctum')->id())
                    ->exists()
            ),
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'rating' => $this->rating,
            'rating_count' => $this->rating_count(),
            'rooms' => $this->rooms,
            'bedrooms' => $this->bedrooms,
            'size' => $this->size,
            'has_pool' => $this->has_pool,
            'has_wifi' => $this->has_wifi,
            // 'cover_image' => $this->cover_image(),
            // 'images' => $this->images,
            'cover_image' => new ApartmentImageResource($this->cover_image()),
            'images' => ApartmentImageResource::collection($this->whenLoaded('images')),
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
