<?php

namespace App\Models;

class Pass extends BaseModel
{

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}
