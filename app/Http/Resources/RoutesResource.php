<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoutesResource extends Resource
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
            'origin' => new CountryResource($this->origin),
            'destination' => new CountryResource($this->destination),
            'transits' => CountryResource::collection($this->whenLoaded('transits')),
            'has_added' => $this->has_added
        ];
    }
}
