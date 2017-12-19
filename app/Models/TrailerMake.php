<?php

namespace App\Models;

class TrailerMake extends BaseModel
{

    protected $hidden = ['name_en','name_ar','name_hi'];

    protected $appends = ['name'];

}
