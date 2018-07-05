<?php

namespace App\Models;

class Trailer extends BaseModel
{

    protected $guarded = ['id'];

    protected $casts = ['length'=>'string','width'=>'string','height'=>'string','max_weight'=>'string'];

    public function type()
    {
        return $this->belongsTo(TrailerType::class);
    }

    public function make()
    {
        return $this->belongsTo(TrailerMake::class);
    }
}
