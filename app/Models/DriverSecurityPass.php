<?php

namespace App\Models;

class DriverSecurityPass extends BaseModel
{

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function security_pass()
    {
        return $this->belongsTo(SecurityPass::class);
    }

}
