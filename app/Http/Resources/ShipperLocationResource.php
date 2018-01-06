<?php

namespace App\Http\Resources;

use App\Models\Truck;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShipperLocationResource extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'state' => $this->state,
            'address' => $this->address,
            'type' => $this->type,
            'shipper' => new ShipperResource($this->whenLoaded('shipper')),
            'country' => new CountryResource($this->whenLoaded('country')),
        ];
    }
}
