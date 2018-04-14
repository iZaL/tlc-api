<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TrailerResource extends Resource
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
            'make' => new TrailerMakeResource($this->whenLoaded('make')),
            'type' => new TrailerTypeResource($this->whenLoaded('type')),
//            'name' => $this->name,
            'max_weight' => $this->max_weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'axles' => $this->axles,
            'year' => $this->year,
            'image' => $this->image,
            'active' => $this->active
        ];
    }
}
