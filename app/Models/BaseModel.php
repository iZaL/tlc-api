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
}
