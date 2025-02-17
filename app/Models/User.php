<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{

    const ADMIN_CODE = 100;
    const DRIVER_CODE = 10;
    const CUSTOMER_CODE = 20;
    const GUEST_CODE = 0;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'otp', 'api_token', 'driver', 'customer', 'updated_at'
    ];

    protected $appends = ['type'];

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function bank_accounts()
    {
        return $this->hasMany(BankAccount::class,'user_id');
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getTypeAttribute()
    {

        if($this->admin) {
            return self::ADMIN_CODE;
        }

        if($this->driver) {
            return self::DRIVER_CODE;
        }

        if($this->customer) {
            return self::CUSTOMER_CODE;
        }

        return self::GUEST_CODE;

    }

//    public function localizeAttribute($attribute)
//    {
//        return $this->{$attribute . '_' . app()->getLocale()} ? : $this->{$attribute . '_' . config('app.fallback_locale')};
//    }

//    public function getNameAttribute()
//    {
//        return $this->localizeAttribute('name');
//    }

}
