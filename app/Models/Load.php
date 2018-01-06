<?php

namespace App\Models;

class Load extends BaseModel
{

    protected $fillable = [
        'shipper_id',
        'trailer_id',
        'origin_location_id',
        'destination_location_id',
        'price',
        'request_documents',
        'request_pictures',
        'fixed_rate',
        'scheduled_at',
        'status',
        'distance'
    ];

    public function origin()
    {
        return $this->belongsTo(ShipperLocation::class,'origin_location_id');
    }

    public function destination()
    {
        return $this->belongsTo(ShipperLocation::class,'destination_location_id');
    }

    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

    public function packaging()
    {
        return $this->belongsTo(Packaging::class);
    }

    public function shipper()
    {
        return $this->belongsTo(Shipper::class);
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class,'load_passes');
    }

    public function fines()
    {
        return $this->belongsToMany(Fine::class,'load_fines');
    }

    public function documentations()
    {
        return $this->belongsToMany(Documentation::class,'load_documentations');
    }


    public function drivers()
    {
        return $this->belongsToMany(Driver::class,'load_drivers');
    }



}

