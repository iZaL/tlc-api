<?php

namespace App\Models;

class Trip extends BaseModel
{

    const STATUS_PENDING = 10;
    const STATUS_APPROVED = 20;
    const STATUS_REJECTED = 50;
    const STATUS_CONFIRMED = 60;
    const STATUS_ENROUTE = 70;
    const STATUS_COMPLETED = 100;

    protected $table = 'trips';

    public function booking()
    {
        return $this->belongsTo(Load::class,'load_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function fines()
    {
        return $this->belongsToMany(Fine::class,'trip_fines');
    }

    public function documents()
    {
        return $this->belongsToMany(DocumentType::class,'trip_documents','document_type_id')->withPivot(['id','amount','url','extension']);
    }

    public function scopePending($query)
    {
        return $query->where('status',self::STATUS_PENDING);
    }

}
