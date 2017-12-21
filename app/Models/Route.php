<?php

namespace App\Models;

class Route extends BaseModel
{
    protected $hidden = ['origin_country_id','destination_country_id'];

    public function origin()
    {
        return $this->belongsTo(Country::class,'origin_country_id');
    }

    public function destination()
    {
        return $this->belongsTo(Country::class,'destination_country_id');
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class,'driver_routes');
    }
}
