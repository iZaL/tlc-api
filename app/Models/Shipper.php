<?php

namespace App\Models;

class Shipper extends BaseModel
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canBookDirect()
    {
        return $this->book_direct;
    }

}
