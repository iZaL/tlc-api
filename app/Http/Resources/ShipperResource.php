<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShipperResource extends Resource
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
            'schema' => 'shippers',
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'book_direct' => $this->book_direct,
            'available_credit' => $this->available_credit,
            'cancellation_fee' => $this->cancellation_fee,
            'active' => $this->active,
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'locations' => LocationResource::collection($this->whenLoaded('locations')),
        ];
    }
}
