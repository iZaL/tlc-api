<?php

namespace App\Models;

class TruckModel extends BaseModel
{
    protected $hidden = ['name_en','name_ar','name_hi'];

    protected $appends = ['name'];

    public function make()
    {
        return $this->belongsTo(TruckMake::class);
    }

}
