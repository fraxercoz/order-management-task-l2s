<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'order_number' => $this->order_number,
            'status'       => $this->status,
            'total'        => $this->total,
            'ordered_at'   => $this->ordered_at
            ? $this->ordered_at->toIso8601String()
            : null,
            'customer' => new CustomerResource($this->whenLoaded('customer')),

            'items' => OrderItemResource::collection(
                $this->whenLoaded('items')
            ),
        ];
    }
}
