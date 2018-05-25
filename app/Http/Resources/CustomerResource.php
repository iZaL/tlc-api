<?php

namespace App\Http\Resources;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerResource extends Resource
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
            'schema' => 'customers',
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'email' => $this->email,
            'image' => $this->user->image,
            'name' => $this->user->name,
            'address' => $this->address,
            'book_direct' => $this->book_direct,
            'available_credit' => $this->available_credit,
            'cancellation_fee' => $this->cancellation_fee,
            'active' => $this->active,
//            'user' => new UserResource($this->whenLoaded('user')),
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'locations' => CustomerLocationResource::collection($this->whenLoaded('locations')),
            'loads' => LoadResource::collection($this->whenLoaded('loads')),
            'blocked_drivers' => DriverResource::collection($this->whenLoaded('blocked_drivers'))
        ];
    }
}
