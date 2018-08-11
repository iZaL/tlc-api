<?php

namespace App\Models;

class TripDocument extends BaseModel
{

    protected $guarded = ['id'];

    public function type()
    {
        return $this->belongsTo(DocumentType::class);
    }

}
