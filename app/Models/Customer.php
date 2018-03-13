<?php

namespace App\Models;

class Customer extends BaseModel
{

    public function loads()
    {
        return $this->hasMany(Load::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canBookDirect()
    {
        return $this->book_direct;
    }

    public function locations()
    {
        return $this->hasMany(CustomerLocation::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
