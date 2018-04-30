<?php

namespace App\Models;

class Load extends BaseModel
{

    const STATUS_PENDING = 10;
    const STATUS_APPROVED = 20;
    const STATUS_REJECTED = 50;
    const STATUS_CONFIRMED = 60;
    const STATUS_ENROUTE = 70;
    const STATUS_COMPLETED = 100;

    // default pending
    // approved (approved by tlc)
    // rejected (the load has been rejected )
    // confirmed (all fleets has been confirmed by drivers) once this status is set, after this no more trip booking allowed
    // enroute (all fleets has dispatched)
    // completed (all fleets unloaded or reached destination)

    protected $fillable = [
        'customer_id',
        'trailer_type_id',
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

    public function trailer_type()
    {
        return $this->belongsTo(TrailerType::class);
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
    public function security_passes()
    {
        return $this->belongsToMany(SecurityPass::class,'load_security_passes');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * @return mixed
     * Trips that are confirmed and beyond
     */
    public function success_trips()
    {
        return $this->trips()
            ->where('status','>',self::STATUS_REJECTED)
            ;
    }

    /**
     * @return int
     * Number of Fleets Remaining for Customers to Book
     */
    public function getPendingFleetsAttribute()
    {
        // get fleet count
        // get successfully booked trips
        return 2;
    }




}

