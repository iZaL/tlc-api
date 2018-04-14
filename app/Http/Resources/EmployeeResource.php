<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeResource extends Resource
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
            'id'                 => $this->id,
            'name'               => $this->name,
            'name_en'            => $this->name_en,
            'name_ar'            => $this->name_ar,
            'name_hi'            => $this->name_hi,
            'email'              => $this->email,
            'mobile'             => $this->mobile,
            'phone'              => $this->phone,
            'image'              => $this->image,
            'active'             => $this->active,
            'driver_interaction' => (bool) $this->driver_interaction,
            'customer'            => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
