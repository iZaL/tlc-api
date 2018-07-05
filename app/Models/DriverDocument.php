<?php

namespace App\Models;

class DriverDocument extends BaseModel
{

    protected $guarded = ['id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
