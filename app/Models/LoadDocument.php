<?php
namespace App\Models;

class LoadDocument extends BaseModel
{
    protected $table = 'load_documents';

    public function loads() {
        return $this->hasMany(Load::class);
    }
}
