<?php

namespace App\Models;

class Driver extends BaseModel
{

    protected $hidden = ['customer_id', 'user_id'];

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

//    public function documents()
//    {
//        return $this->belongsToMany(Country::class,'driver_documents')->withPivot(['expiry_date', 'number', 'image']);
//    }

    /**
     * @todo:for now allow only has one
     */
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function nationalities()
    {
        return $this->documents()->where('type','nationality');
    }

    public function residencies()
    {
        return $this->documents()->where('type','residency');
    }

    public function visas()
    {
        return $this->documents()->where('type','visa');
    }

    public function licenses()
    {
        return $this->documents()->where('type','license');
    }

    public function valid_visas()
    {
        return $this->visas()->where('expiry_date', '>', date('Y-m-d'));
    }

    public function valid_licenses()
    {
        return $this->licenses()->where('expiry_date', '>', date('Y-m-d'));
    }

    public function security_passes()
    {
//        return $this->hasMany(DriverSecurityPass::class,'driver_id');
        return $this->belongsToMany(SecurityPass::class,'driver_security_passes')->withPivot([
            'id','image','expiry_date'
        ]);
    }

    public function blocked_list()
    {
        return $this->belongsToMany(Customer::class, 'blocked_drivers');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'driver_routes');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function blocked_dates()
    {
        return $this->hasMany(DriverBlockedDate::class);
    }

    public function getIsLegitAttribute()
    {
        return $this->book_direct;
    }
}
