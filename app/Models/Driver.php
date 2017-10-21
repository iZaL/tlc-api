<?php

namespace App\Models;

class Driver extends BaseModel
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visas()
    {
        return $this->belongsToMany(Country::class,'driver_visas')
            ->withPivot('expiry_date')
//            ->wherePivot('expiry_date','<',date('Y-m-d'))
            ;
    }

    public function validVisas()
    {
        return $this->visas()->wherePivot('expiry_date','>',date('Y-m-d'));

    }

}
