<?php

namespace App\Http\Resources\V2\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->resource->id,
            'name'     => $this->resource->name,
            'email'    => $this->resource->email,
            'phone'    => $this->resource->phone,
            'gender'   => $this->resource->gender,
            // 'address'  => $this->resource->address,
            // 'city'     => $this->resource->city,
            // 'type'     => $this->resource->type,
            // 'verifyed_at' => $this->resource->email_verified_at,
            'email_verified_at' => $this->resource->email_verified_at,
            'phone_verify_at' => $this->resource->phone_verify_at,
            'defaultShippingAddress' => $this->resource->defaultShippingAddress ?? null,

        ];
    }

    public function with($request)
    {
        return ['message' => 'get data successfully', 'success' => true];
    }
}
