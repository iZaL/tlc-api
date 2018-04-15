<?php

namespace App\Http\Resources;

use App\Models\DriverSecurityPass;
use App\Models\Truck;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DriverResource extends Resource
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
            'id'              => $this->id,
            'schema'          => 'drivers',
            'mobile'          => $this->mobile,
            'phone'           => $this->phone,
            'book_direct'     => $this->book_direct,
            'active'          => $this->active,
            'offline'         => $this->offline,
            'blocked'         => $this->blocked,
            'user'            => new UserResource($this->whenLoaded('user')),
            'truck'           => new TruckResource($this->whenLoaded('truck')),
            'customer'        => new CustomerResource($this->whenLoaded('customer')),
            'routes'          => RoutesResource::collection($this->whenLoaded('routes')),
            'loads'           => LoadResource::collection($this->whenLoaded('loads')),
            'nationalities'   => DocumentResource::collection($this->whenLoaded('nationalities')),
            'residencies'     => DocumentResource::collection($this->whenLoaded('residencies')),
            'visas'           => DocumentResource::collection($this->whenLoaded('visas')),
            'licenses'        => DocumentResource::collection($this->whenLoaded('licenses')),
            'security_passes' => DriverSecurityPassResource::collection($this->whenLoaded('security_passes')),
        ];
    }
}
