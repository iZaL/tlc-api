<?php

namespace App\Models;

class Truck extends BaseModel
{

    protected $fillable = ['make_id','model_id','plate_number','registration_number','registration_expiry','year','max_weight','trailer_id'];
    protected $hidden = ['make_id','model_id','trailer_id'];

    public function model()
    {
        return $this->belongsTo(TruckModel::class);
    }

    public function make()
    {
        return $this->belongsTo(TruckMake::class);
    }

//    public function driver()
//    {
//        return $this->belongsTo(Driver::class);
//    }

    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

}
