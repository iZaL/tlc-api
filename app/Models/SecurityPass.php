<?php

namespace App\Models;

class SecurityPass extends BaseModel
{

    protected $guarded = ['id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}
