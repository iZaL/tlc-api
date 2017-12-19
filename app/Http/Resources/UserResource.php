<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResource extends Resource
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
            'success' => true,
            'data'    => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'admin' => $this->admin,
                'image' => $this->image,
                'active' => $this->active,
                'profile' => $this->when($this->driver || $this->shipper, function () {
                    if($this->driver) {
                        return new DriverResource($this->driver);
                    }
                }),
            ],
        ];
    }
}
