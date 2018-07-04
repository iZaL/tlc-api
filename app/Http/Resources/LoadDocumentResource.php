<?php

namespace App\Http\Resources;

use App\Models\DocumentType;
use Illuminate\Http\Resources\Json\Resource;

class LoadDocumentResource extends Resource
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
            'url' => $this->url,
            'extension' => $this->extension,
            'type' => $this->type,
        ];
    }
}
