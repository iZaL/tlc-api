<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

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
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'mobile'  => $this->mobile,
            'image'   => $this->image,
            'active'  => $this->active,
            'type'    => $this->type,
            'admin'   => $this->when($this->admin, true),
            'profile' => $this->when(Auth::guard('api')->user() && Auth::guard('api')->user()->id === $this->id || auth()->check() && auth()->user()->id === $this->id, function () {
                if ($this->type === 10) {
                    return new DriverResource($this->driver);
                } elseif ($this->type === 20) {
                    return new CustomerResource($this->customer);
                }
                return null;
            }),
        ];
    }
}
