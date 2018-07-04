<?php

namespace App\Http\Resources;

use App\Models\LoadDocument;
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
            'track_id' => $this->track_id,
            'use_own_truck' => $this->use_own_truck,
            'load_date_formatted' => $this->load_date_formatted, // Jan 4
            'load_time_formatted' => $this->load_time_formatted, // 1-10am
            'unload_date_formatted' => $this->unload_date_formatted, // Jan 4 (1-10am)
            'unload_time_formatted' => $this->unload_time_formatted, // Jan 4 (1-10am)
            'price_formatted' => $this->price_formatted,
            'weight' => $this->weight,
            'weight_formatted' => $this->weight .  trans('g.tons'),
            'status_formatted' => $this->status_formatted,
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
            'trip' => new TripDriverResource($this->whenLoaded('trip')),
            'packaging' => (new PackagingResource($this->whenLoaded('packaging')))->additional(['length' => $this->packaging_length]),
            'packaging_dimensions' => [
                'length_formatted' => $this->packaging_length_formatted,
                'width_formatted' => $this->packaging_width_formatted,
                'height_formatted' => $this->packaging_height_formatted,
                'weight' => $this->packaging_weight,
                'quantity' => $this->packaging_quantity,
            ],
            'packaging_images' => LoadDocumentResource::collection($this->whenLoaded('packaging_images')),
            'commodity' => new CommodityResource($this->whenLoaded('commodity')),
            'receiver' => [
                'name'=>$this->receiver_name,
                'mobile'=>$this->receiver_mobile,
                'phone'=>$this->receiver_phone,
                'email'=>$this->receiver_email,
            ],

        ];
    }
}
