<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DriverSecurityPassResource extends Resource
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
            'id' => $this->pivot->id,
            'expiry_date' => $this->pivot->expiry_date,
            'image' => $this->pivot->image,
//            'number' => $this->number,
            'security_pass' => new SecurityPassResource($this),
        ];
    }
}
