<?php

namespace App\Models;

class Driver extends BaseModel
{

    protected $fillable = ['mobile', 'nationality_country_id', 'user_id', 'customer_id'];

    protected $hidden = ['nationality_country_id', 'customer_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @todo:for now allow only has one
     */
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality_country_id');
    }

    public function residencies()
    {
        return $this->belongsToMany(Country::class, 'driver_residencies')
            ->withPivot(['expiry_date', 'number', 'image']);
    }

    public function visas()
    {
        return $this->belongsToMany(Country::class, 'driver_visas')
            ->withPivot(['expiry_date', 'number', 'image']);
    }

    public function licenses()
    {
        return $this->belongsToMany(Country::class, 'driver_licenses')
            ->withPivot(['expiry_date', 'number', 'image']);
    }

    public function valid_visas()
    {
        return $this->visas()->wherePivot('expiry_date', '>', date('Y-m-d'));
    }

    public function valid_licenses()
    {
        return $this->licenses()->wherePivot('expiry_date', '>', date('Y-m-d'));
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class, 'driver_passes');
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

//    public function loads()
//    {
//        return $this->belongsToMany(Load::class, 'jobs');
//    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function blocked_dates()
    {
        return $this->hasMany(DriverBlockedDate::class);
    }

}
