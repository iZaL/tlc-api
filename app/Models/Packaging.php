<?php
namespace App\Models;

class Packaging extends BaseModel
{
    protected $table = 'packagings';

    public function loads() {
        return $this->hasMany(Load::class);
    }
}
