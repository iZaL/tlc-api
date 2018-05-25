<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TripResource extends Resource
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
            'amount' => $this->amount,
            'reached_at' => $this->reached_at,
            'status' => $this->status,
            'load_id' => $this->load_id,
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'documents' => TripDocumentResource::collection($this->whenLoaded('documents'))
        ];
    }
}
