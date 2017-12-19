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
            'id'                  => $this->id,
            'make'                => [
                'id' => $this->make->id,
                'name' => $this->make->name,
            ],
            'model'               => [
                'id' => $this->model->id,
                'name' => $this->model->name,
            ],
            'trailer'             => new TrailerResource($this->whenLoaded('trailer')),
            'plate_number'        => $this->plate_number,
            'registration_number' => $this->registration_number,
            'registration_expiry' => $this->registration_expiry,
            'max_weight'          => $this->max_weight,
            'year'                => $this->year,
            'image'               => $this->image,
            'latitude'            => $this->latitude,
            'longitude'           => $this->longitude,
        ];
    }
}
