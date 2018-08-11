<?php

namespace App\Models;

class BankAccount extends BaseModel
{

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

//    public function getNameAttribute()
//    {
//        return $this->name;
//    }

}
