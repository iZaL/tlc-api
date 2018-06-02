<?php

namespace App\Models;

class Load extends BaseModel
{

    const STATUS_PENDING = 10;
    const STATUS_ACCEPTED = 15;//@remove
    const STATUS_APPROVED = 20;//@remove
    const STATUS_CONFIRMED = 30;
    const STATUS_DISPATCHED = 40;//@remove
    const STATUS_OFFLOADED = 45;//@remove
    const STATUS_COMPLETED = 70;

    const STATUS_REJECTED = 80;
    const STATUS_CANCELLED = 90; //@remove

    // default pending
    // approved (approved by tlc)
    // rejected (the load has been rejected )
    // confirmed (all fleets has been confirmed by drivers) once this status is set, after this no more trip booking allowed
    // dispatched (all fleets has dispatched)
    // completed (all fleets unloaded or reached destination)

    protected $fillable = [
        'customer_id',
        'trailer_type_id',
        'packaging_id',
        'origin_location_id',
        'destination_location_id',
        'request_documents',
        'request_pictures',
        'fixed_rate',
        'load_date',
        'unload_date',
        'load_time_from',
        'load_time_to',
        'receiver_name',
        'receiver_email',
        'receiver_phone',
        'receiver_mobile',
        'use_own_truck',
        'packaging_width',
        'packaging_height',
        'packaging_length',
        'packaging_quantity',
        'packaging_weight',
        'status',
        'weight',
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
     * API specific
     * Auth Driver Trip
     */
    public function trip()
    {
        $driver = auth()->guard('api')->user()->driver;
        return $this->hasOne(Trip::class)->where('driver_id',$driver->id);
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

    public function getLoadDateFormattedAttribute()
    {
        return 'Jan 4';
    }

    public function getUnloadDateFormattedAttribute()
    {
        return 'Jan 8';
    }

    public function getLoadTimeFormattedAttribute()
    {
        return '1am-10pm';
    }

    public function getUnloadTimeFormattedAttribute()
    {
        return '1am-10pm';
    }

    public function getPriceFormattedAttribute()
    {
        return $this->price . $this->getUserCurrency();
    }

    public function getStatusFormattedAttribute()
    {
        $currentStatus = isset($this->attributes['status']) ? :'pending';

        switch ($currentStatus) {
            case self::STATUS_PENDING :
                return __('g.pending');
            case self::STATUS_CANCELLED :
                return __('g.cancelled');
            case self::STATUS_APPROVED :
                return __('g.approved');
            case self::STATUS_REJECTED :
                return __('g.rejected');
            case self::STATUS_CONFIRMED :
                return __('g.confirmed');
            case self::STATUS_DISPATCHED :
                return __('g.dispatched');
            case self::STATUS_COMPLETED :
                return __('g.completed');
            default :
                return null;

        }
    }

//    public function getStatusNameAttribute()
//    {
//        $currentStatus = $this->attributes['status'];
//
//        switch ($currentStatus) {
//            case self::STATUS_PENDING :
//                return __('pending');
//            case self::STATUS_CANCELLED :
//                return __('cancelled');
//            case self::STATUS_APPROVED :
//                return __('approved');
//            case self::STATUS_REJECTED :
//                return __('rejected');
//            case self::STATUS_CONFIRMED :
//                return __('confirmed');
//            case self::STATUS_DISPATCHED :
//                return __('dispatched');
//            case self::STATUS_COMPLETED :
//                return __('completed');
//            default :
//                return null;
//
//        }
//    }


}


