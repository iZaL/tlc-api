<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UploadResource extends Resource
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
            'type' => $this->type
        ];
    }
}
