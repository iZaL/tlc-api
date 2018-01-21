<?php

namespace App\Models;

class Trip extends BaseModel
{

    protected $table = 'trips';

    public function booking()
    {
        return $this->belongsTo(Load::class,'load_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function fines()
    {
        return $this->belongsToMany(Fine::class,'trip_fines');
    }

    public function documentations()
    {
        return $this->belongsToMany(Documentation::class,'trip_documentations');
    }

    public function confirmTrip()
    {

    }

    public function scopePending($query)
    {
        return $query->where('status','pending');
    }

}
