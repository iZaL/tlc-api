<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TripDriverResource extends Resource
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
            'can_accept' => $this->can_accept,
            'can_cancel' => $this->can_cancel,
            'can_start'   => $this->can_start,
            'can_stop'    => $this->can_stop,
            'status' => $this->status,
            'status_formatted' => $this->status_formatted,
            'documents' => TripDocumentResource::collection($this->whenLoaded('documents')),
        ];
    }

}
