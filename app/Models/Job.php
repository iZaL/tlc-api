<?php

namespace App\Models;

class Job extends BaseModel
{

    public function fines()
    {
        return $this->belongsToMany(Fine::class,'job_fines');
    }

    public function documentations()
    {
        return $this->belongsToMany(Documentation::class,'job_documentations');
    }

}
