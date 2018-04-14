<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Route extends BaseModel
{
    protected $hidden = ['origin_country_id', 'destination_country_id'];

//    protected $with = ['drivers'];

    public function origin()
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

    public function destination()
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_routes');
    }

    public function transits()
    {
        return $this->belongsToMany(Country::class, 'route_transits')->orderBy('order', 'asc');
    }

    /**
     * Whether the Driver has added the Route
     */
    public function getHasAddedAttribute()
    {
        if (Auth::guard('api')->check()) {
            $driver = Auth::guard('api')->user()->driver;
            if ($driver) {
                return $this->drivers->contains($driver->id);
            }
        }

        return false;
    }

    public function locations()
    {
//        return $this->
    }


}
