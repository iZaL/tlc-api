<?php

namespace App\Models;

class DriverResidency extends BaseModel
{

    protected $with = ['country'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

}
