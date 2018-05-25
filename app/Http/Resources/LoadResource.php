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
            'date' => $this->load_date,
            'time' => $this->load_time,
            'status' => $this->status,
            'fixed_rate' => $this->fixed_rate,
            'distance' => $this->distance,
            'pending_fleets' => $this->pending_fleets, // fleets remaing to get booked
            'request_documents' => $this->request_documents,
            'request_pictures' => $this->request_pictures,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'origin' => new CustomerLocationResource($this->whenLoaded('origin')),
            'destination' => new CustomerLocationResource($this->whenLoaded('destination')),
            'trailer_type' => new TrailerTypeResource($this->whenLoaded('trailer_type')),
            'trips' => TripResource::collection($this->whenLoaded('trips')),
            'trip' => new TripResource($this->whenLoaded('trip')),
            'receiver' => [
                'name'=>$this->receiver_name,
                'mobile'=>$this->receiver_mobile,
                'phone'=>$this->receiver_phone,
                'email'=>$this->receiver_email,
            ],

        ];
    }
}
