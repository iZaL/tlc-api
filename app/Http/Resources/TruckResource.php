<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TruckResource extends Resource
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
            'id'                       => $this->id,
            'model'                    => new TruckModelResource($this->whenLoaded('model')),
            'trailer'                  => new TrailerResource($this->whenLoaded('trailer')),
            'registration_country'     => new CountryResource($this->whenLoaded('registration_country')),
            'plate_number'             => $this->plate_number,
            'registration_number'      => $this->registration_number,
            'registration_expiry_date' => $this->registration_expiry_date,
            'registration_image'       => $this->registration_image,
            'max_weight'               => $this->max_weight,
            'year'                     => $this->year,
            'image'                    => $this->image,
            'latitude'                 => $this->latitude,
            'longitude'                => $this->longitude,
        ];
    }
}
