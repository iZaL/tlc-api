<?php

namespace App\Models;

class Trip extends BaseModel
{

    protected $table = 'trips';

    public function fines()
    {
        return $this->belongsToMany(Fine::class,'trip_fines');
    }

    public function documentations()
    {
        return $this->belongsToMany(Documentation::class,'trip_documentations');
    }

}
