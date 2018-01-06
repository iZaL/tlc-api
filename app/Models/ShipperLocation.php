<?php

namespace App\Models;

class ShipperLocation extends BaseModel
{

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',

    ];
//    protected $appends = ['type'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getCityAttribute()
    {
        return $this->localizeAttribute('city');
    }

    public function getStateAttribute()
    {
        return $this->localizeAttribute('state');
    }

    public function getAddressAttribute()
    {
        return $this->localizeAttribute('address');
    }

}
