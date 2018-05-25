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

    public function documents()
    {
        return $this->hasMany(DocumentType::class);
    }

    public function blocked_drivers()
    {
        return $this->belongsToMany(Driver::class, 'blocked_drivers');
    }

    public function getEmailAttribute()
    {
        return $this->attributes['email'] ? : $this->user->email;
    }

    public function getMobileAttribute()
    {
        return $this->attributes['mobile'] ?: $this->user->mobile;
    }


}
