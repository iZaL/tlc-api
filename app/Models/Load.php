<?php

namespace App\Models;

class Load extends BaseModel
{

    protected $fillable = [
        'customer_id',
        'trailer_id',
        'packaging_id',
        'origin_location_id',
        'destination_location_id',
        'price',
        'request_documents',
        'request_pictures',
        'fixed_rate',
        'load_date',
        'load_time',
        'receiver_name',
        'receiver_email',
        'receiver_phone',
        'receiver_mobile',
        'status',
        'weight',
        'use_own_truck',
    ];

    public function origin()
    {
        return $this->belongsTo(CustomerLocation::class,'origin_location_id');
    }

    public function destination()
    {
        return $this->belongsTo(CustomerLocation::class,'destination_location_id');
    }

    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

    public function packaging()
    {
        return $this->belongsTo(Packaging::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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

