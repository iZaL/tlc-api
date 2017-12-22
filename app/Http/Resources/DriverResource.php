<?php

namespace App\Http\Resources;

use App\Models\Truck;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DriverResource extends Resource
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
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'book_direct' => $this->book_direct,
            'status' => $this->status,
            'active' => $this->active,
            'blocked' => $this->blocked,
            'nationality' => new CountryResource($this->whenLoaded('nationality')),
            'residence' => new CountryResource($this->whenLoaded('residence')),
            'truck' => new TruckResource($this->whenLoaded('truck')),
            'routes' => RoutesResource::collection($this->whenLoaded('routes')),
            'available_routes' => RoutesResource::collection($this->whenLoaded('available_routes')),
            'visas' => CountryResource::collection($this->whenLoaded('visas')),
            'licenses' => CountryResource::collection($this->whenLoaded('licenses')),
            'shipper' => new ShipperResource($this->whenLoaded('shipper')),
//            'profile' =>  new DriverResource($this->driver)
        ];
    }
}
