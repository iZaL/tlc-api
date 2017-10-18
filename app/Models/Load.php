<?php

namespace App\Models;

class Load extends BaseModel
{

    protected $fillable = [
        'shipper_id',
        'trailer_id',
        'origin_location_id',
        'destination_location_id',
        'price',
        'request_documents',
        'request_pictures',
        'fixed_rate',
        'scheduled_at',
        'status',
        'distance'
    ];

}
