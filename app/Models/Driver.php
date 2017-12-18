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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @todo:for now allow only has one
     */
    public function truck()
    {
//        return $this->hasMany(Truck::class);
        return $this->hasOne(Truck::class);
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
        return $this->belongsToMany(Country::class,'driver_visas')->withPivot('expiry_date');
    }

    public function licenses()
    {
        return $this->belongsToMany(Country::class,'driver_licenses')->withPivot(['expiry_date','number']);
    }

    public function validVisas()
    {
        return $this->visas()->wherePivot('expiry_date','>',date('Y-m-d'));
    }

    public function validLicenses()
    {
        return $this->licenses()->wherePivot('expiry_date','>',date('Y-m-d'));
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class,'driver_passes');
    }

    public function blockedList()
    {
        return $this->belongsToMany(Shipper::class,'blocked_drivers');
    }

    public function shipper()
    {
        return $this->belongsTo(Shipper::class);
    }

}
