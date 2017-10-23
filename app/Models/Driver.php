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
        return $this->belongsToMany(Country::class,'driver_visas')->withPivot('expiry_date');
    }

    public function licenses()
    {
        return $this->belongsToMany(Country::class,'driver_licenses')->withPivot(['expiry_date','number']);
    }

    public function validVisas()
    {
        return $this->visas()->wherePivot('expiry_date','>',date('Y-m-d'));
    }

    public function validLicenses()
    {
        return $this->licenses()->wherePivot('expiry_date','>',date('Y-m-d'));
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class,'driver_passes');
    }

}
