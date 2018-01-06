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
                'id'     => $this->id,
                'name'   => $this->name,
                'email'  => $this->email,
                'mobile' => $this->mobile,
                'image'  => $this->image,
                'active' => $this->active,
                'type'   => $this->type,
                'admin'  => $this->when($this->admin, true),
                'profile' => $this->when($this->type === 10, function () {
                    return new DriverResource($this->driver);
                }),
            ],
        ];
    }
}
