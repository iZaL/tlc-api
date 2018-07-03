<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const UPLOAD_PATH = '/uploads/';

    public function customValidate($requestArray, $rules)
    {

        $validator = Validator::make($requestArray->all(),$rules);

        if($validator->fails()) {
            return response()->json(['success'=>false,'message'=>$validator->errors()->first()],422);
        }

        return true;

    }

    protected function uploadImage($image)
    {
        if (!$image->isValid()) {
            throw new \Exception('invalid image');
        }

        $imageName = md5(uniqid(rand() * (time()))) . '.' . $image->getClientOriginalExtension();
        $savePath = public_path() . self::UPLOAD_PATH . $imageName;

        Image::make($image)->save($savePath, 90);

        $fullImagePath = app()->make('url')->to(self::UPLOAD_PATH . $imageName);

        return $fullImagePath;

    }
}
