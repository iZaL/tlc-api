<?php

namespace App\Http\Resources;

use App\Models\DocumentType;
use Illuminate\Http\Resources\Json\Resource;

class TripDocumentResource extends Resource
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
            'id' => $this->pivot->id,
            'amount' => $this->pivot->amount,
            'url' => $this->pivot->url,
            'extension' => $this->pivot->extension,
            'type' => new DocumentTypeResource($this),
        ];
    }
}
