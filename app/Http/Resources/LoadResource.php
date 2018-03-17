<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LoadResource extends Resource
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
            'price' => $this->price,
            'invoice_id' => $this->invoice_id,
            'use_own_truck' => $this->use_own_truck,
//            'scheduled_at' => $this->scheduled_at,
            'date' => $this->load_date,
            'time' => $this->load_time,
            'status' => $this->status,
            'fixed_rate' => $this->fixed_rate,
            'distance' => $this->distance,
            'fleet_count' => $this->fleet_count,
            'request_documents' => $this->request_documents,
            'request_pictures' => $this->request_pictures,
//            'created_at' => $this->created_at,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'origin' => new CustomerLocationResource($this->whenLoaded('origin')),
            'destination' => new CustomerLocationResource($this->whenLoaded('destination')),
            'trailer' => new TrailerResource($this->whenLoaded('trailer')),
        ];
    }
}
