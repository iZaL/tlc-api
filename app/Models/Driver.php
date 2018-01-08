<?php

namespace App\Models;

class Driver extends BaseModel
{

    protected $fillable = ['mobile','residence_country_id','nationality_country_id','user_id','shipper_id'];

    protected $hidden = ['nationality_country_id','residence_country_id','shipper_id','user_id'];

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
        return $this->belongsTo(Country::class,'nationality_country_id');
    }

    public function residence()
    {
        return $this->belongsTo(Country::class,'residence_country_id');
    }

    public function visas()
    {
        return $this->belongsToMany(Country::class,'driver_visas')
            ->withPivot(['expiry_date','number','image'])
            ;
    }

    public function licenses()
    {
        return $this->belongsToMany(Country::class,'driver_licenses')
            ->withPivot(['expiry_date','number','image'])
            ;
    }

    public function valid_visas()
    {
        return $this->visas()->wherePivot('expiry_date','>',date('Y-m-d'));
    }

    public function valid_licenses()
    {
        return $this->licenses()->wherePivot('expiry_date','>',date('Y-m-d'));
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class,'driver_passes');
    }

    public function blocked_list()
    {
        return $this->belongsToMany(Shipper::class,'blocked_drivers');
    }

    public function shipper()
    {
        return $this->belongsTo(Shipper::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class,'driver_routes');
    }

    public function loads()
    {
        return $this->belongsToMany(Load::class,'jobs');
    }

}
