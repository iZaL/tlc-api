<?php

namespace App\Models;

class Shipper extends BaseModel
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
