<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        $query->where('active',1);
    }

    public function scopeOfStatus($query,$status)
    {
        return $query->where('status',$status);
    }

    public function localizeAttribute($attribute)
    {
        return $this->{$attribute . '_' . app()->getLocale()} ? : $this->{$attribute . '_' . config('app.fallback_locale')};
    }

    public function getNameAttribute()
    {
        return $this->localizeAttribute('name');
    }

    protected function getUserCurrency()
    {
        return 'KWD';
    }

    protected function getExchangedRate($amount, $formatted=false, $toCurrency='KWD', $fromCurrency='USD' ) {
        return $formatted ? round($amount).' KWD':round($amount);
//        $rate =  $amount;
//       return  currency($amount,$fromCurrency,$toCurrency,$formatted);
    }

}
