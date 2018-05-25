<?php

namespace App\Models;

class TripDocument extends BaseModel
{

    public function type()
    {
        return $this->belongsTo(DocumentType::class);
    }

}
