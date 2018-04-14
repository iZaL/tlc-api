<?php

namespace App\Models;

class Trailer extends BaseModel
{

    protected $appends = ['name'];

    protected $hidden = ['make_id','name_en','name_ar','name_hi'];

    public function type()
    {
        return $this->belongsTo(TrailerType::class);
    }

    public function make()
    {
        return $this->belongsTo(TrailerMake::class);
    }
}
