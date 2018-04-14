<?php

namespace App\Models;

class TruckMake extends BaseModel
{
    protected $hidden = ['name_en','name_ar','name_hi'];

    protected $appends = ['name'];

    public function models()
    {
        return $this->hasMany(TruckModel::class,'make_id');
    }

}
