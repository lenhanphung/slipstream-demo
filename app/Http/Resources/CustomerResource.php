<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'reference' => $this->reference,
            'category' => new CustomerCategoryResource($this->whenLoaded('category')),
            'customer_category_id' => $this->customer_category_id,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'description' => $this->description,
            'contacts_count' => $this->contacts_count ?? $this->whenCounted('contacts'),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
