<?php

namespace App\Models;

class Truck extends BaseModel
{

    protected $fillable = ['make_id','model_id','plate_number','registration_number','registration_country_id','registration_expiry_date','year','max_weight','trailer_id'];
    protected $hidden = ['make_id','model_id','trailer_id'];

    public function model()
    {
        return $this->belongsTo(TruckModel::class);
    }

    public function registration_country()
    {
        return $this->belongsTo(Country::class,'registration_country_id');
    }

    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

}
