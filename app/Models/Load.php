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
        'load_date',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * Requires Passes
     */
    public function passes()
    {
        return $this->belongsToMany(Pass::class,'load_passes');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }


}

