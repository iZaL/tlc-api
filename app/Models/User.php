<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','api_token'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'otp', 'api_token', 'driver', 'shipper', 'updated_at'
    ];

    protected $appends = ['type','profile'];

    public function shipper()
    {
        return $this->hasOne(Shipper::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getTypeAttribute()
    {

        if($this->admin) {
            return 'admin';
        }

        if($this->driver) {
            return 'driver';
        }

        if($this->shipper) {
            return 'shipper';
        }

        return 'default';

    }

    public function getProfileAttribute()
    {
        switch ($this->type) {
            case 'driver' :
                return $this->driver;
            case 'shipper' :
                return $this->shipper;
            default:
                return null;
        }
    }
}
