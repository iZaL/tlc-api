<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function customValidate($requestArray, $rules)
    {

        $validator = Validator::make($requestArray->all(),$rules);

        if($validator->fails()) {
            return response()->json(['success'=>false,'message'=>$validator->errors()->first()],422);
        }

        return true;

    }
}
