<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LicenseVisaResource extends Resource
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
            'expiry_date' => $this->resource->pivot->expiry_date,
            'number'      => $this->resource->pivot->number,
            'image'      => $this->resource->pivot->image,
            'country'     => new CountryResource($this->resource),
            'profile' => $this->when($this->type === 10, function () {
                return new DriverResource($this->driver);
            }),
        ];
    }
}
