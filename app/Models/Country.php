<?php

namespace App\Models;

class Country extends BaseModel
{

    public function loading_routes()
    {
        return $this->hasMany(Route::class,'origin_country_id');
    }

    public function destination_routes()
    {
        return $this->hasMany(Route::class,'destination_country_id');
    }

    public function getNameAttribute()
    {
        $attribute = 'name';
        return $this->{$attribute . '_' . app()->getLocale()} ? : $this->{$attribute . '_en'};
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

}
