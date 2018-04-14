<?php

namespace App\Models;

class Location extends BaseModel
{

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

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
