<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryResource extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'name' => $this->name,
            'loading_routes' => RoutesResource::collection($this->whenLoaded('loading_routes')),
            'destination_routes' => RoutesResource::collection($this->whenLoaded('destination_routes')),
        ];
    }
}
