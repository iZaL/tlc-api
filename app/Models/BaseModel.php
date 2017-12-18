<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        $query->where('active',1);
    }
}
