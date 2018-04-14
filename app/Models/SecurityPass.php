<?php

namespace App\Models;

class SecurityPass extends BaseModel
{

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}
