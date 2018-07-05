<?php

namespace App\Models;

class Upload extends BaseModel
{
    protected $guarded = ['id'];

    public function entity() {
        return $this->morphTo();
    }
}
