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
            'load_id' => $this->load_id,
            'track_id' => $this->track_id,
            'rate' => $this->rate,
            'rate_formatted' => $this->rate_formatted,
//            'started_at' => $this->started_at,
//            'reached_at' => $this->reached_at,
            'status' => $this->status,
            'status_formatted' => $this->status_formatted,
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'documents' => TripDocumentResource::collection($this->whenLoaded('documents')),
        ];
    }

}
