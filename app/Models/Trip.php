<?php

namespace App\Models;

use App\Managers\TripManager;

class Trip extends BaseModel
{

    const STATUS_PENDING = 10;
    const STATUS_ACCEPTED = 15;
    const STATUS_APPROVED = 20;
    const STATUS_CONFIRMED = 30;
    const STATUS_DISPATCHED = 40;
    const STATUS_OFFLOADED = 45;
    const STATUS_COMPLETED = 70;

    const STATUS_REJECTED = 80;
    const STATUS_CANCELLED = 90;

    protected $casts = ['status' => 'int'];

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

    public function documents()
    {
        return $this->belongsToMany(DocumentType::class,'trip_documents','document_type_id')->withPivot(['id','amount','url','extension']);
    }

    public function scopePending($query)
    {
        return $query->where('status',self::STATUS_PENDING);
    }

    public function getRateAttribute()
    {
        return $this->getExchangedRate($this->attributes['rate']);
    }

    public function getRateFormattedAttribute()
    {
        return $this->getExchangedRate($this->attributes['rate'],true);
    }

    public function getStatusFormattedAttribute()
    {
        $currentStatus = $this->attributes['status'];

        switch ($currentStatus) {
            case self::STATUS_PENDING :
                return __('g.pending');
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

    /**
     * @return bool
     */
    public function getCanAcceptAttribute()
    {
        $driver = optional(auth()->guard('api')->user())->driver;
        $trip = $this;
        if($driver) {
            $tripManager = new TripManager($trip,$driver);
            return $tripManager->canAcceptTrip();
        }
        return false;
    }

    public function getCanCancelAttribute()
    {
        $driver = optional(auth()->guard('api')->user())->driver;
        $trip = $this;
        if($driver) {
            $tripManager = new TripManager($trip,$driver);
            return $tripManager->canCancelTrip();
        }
        return false;
    }

    public function getCanConfirmAttribute()
    {
        $driver = optional(auth()->guard('api')->user())->driver;
        $trip = $this;
        if($driver) {
            $tripManager = new TripManager($trip,$driver);
            return $tripManager->canConfirmTrip();
        }
        return false;
    }

}
