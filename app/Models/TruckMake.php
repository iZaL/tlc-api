<?php

namespace App\Models;

class TruckMake extends BaseModel
{
    protected $hidden = ['name_en','name_ar','name_hi'];

    protected $appends = ['name'];
}
