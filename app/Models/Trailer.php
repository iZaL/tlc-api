<?php

namespace App\Models;

class Trailer extends BaseModel
{

    protected $appends = ['name'];

    public function make()
    {
        return $this->belongsTo(TrailerMake::class);
    }
}
