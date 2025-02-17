<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationResource extends Resource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'abbr'      => $this->abbr,
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'country'   => new CountryResource($this->whenLoaded('country')),
            'has_added' => true,
        ];
    }
}
