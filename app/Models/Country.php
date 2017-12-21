<?php

namespace App\Models;

class Country extends BaseModel
{

    public function loading_routes()
    {
        return $this->hasMany(Route::class,'origin_country_id');
    }

}
