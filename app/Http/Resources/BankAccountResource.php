<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BankAccountResource extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'name'               => $this->name,
            'address'               => $this->address,
            'account_number' => $this->account_number,
            'iban'               => $this->iban,
            'beneficiary_name'               => $this->beneficiary_name,
            'beneficiary_address'               => $this->beneficiary_address,
        ];
    }
}
