<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DocumentResource extends Resource
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
            'expiry_date' => $this->expiry_date,
            'image' => $this->image,
            'number' => $this->number,
//            'country' => new CountryResource($this->whenLoaded('country')),
            'country' => new CountryResource($this->country),
        ];
    }
}
