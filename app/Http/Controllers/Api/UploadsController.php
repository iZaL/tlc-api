<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SecurityPass;
use Illuminate\Http\Request;

class UploadsController extends Controller
{

    /**
     * @var SecurityPass
     */
    private $securityPassModel;

    /**
     * CategoriesController constructor.
     * @param SecurityPass $securityPassModel
     */
    public function __construct(SecurityPass $securityPassModel)
    {
        $this->securityPassModel = $securityPassModel;
    }

    public function uploadImages(Request $request)
    {

        $this->validate($request,[
            'images' => 'required|array',
            'images.*' => 'image'
        ]);

        $uploadedImages = [];

        foreach ($request->images as $image) {
            try {
                $uploadedImage = $this->uploadImage($image);
                $uploadedImages[] = $uploadedImage;
            } catch (\Exception $e){
            };
        }

        return response()->json(['success'=>true,'data'=> $uploadedImages]);

    }

}

