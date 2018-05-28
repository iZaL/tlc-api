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
            'status' => $this->status,
            'status_formatted' => $this->status_formatted,
            'load_id' => $this->load_id,
            'track_id' => $this->track_id,
            'rate' => $this->rate,
            'rate_formatted' => $this->rate_formatted,
            'started_at' => $this->started_at,
            'reached_at' => $this->reached_at,
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'documents' => TripDocumentResource::collection($this->whenLoaded('documents')),
            'can_accept' => $this->can_accept,
            'can_cancel' => $this->can_cancel,
            'can_confirm' => $this->can_confirm
        ];
    }



}
