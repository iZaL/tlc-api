<?php

namespace App\Models;

class Employee extends BaseModel
{
    protected $table = 'employees';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
