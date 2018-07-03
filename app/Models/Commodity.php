<?php
namespace App\Models;

class Commodity extends BaseModel
{
    protected $table = 'commodities';

    public function loads() {
        return $this->hasMany(Load::class);
    }
}
