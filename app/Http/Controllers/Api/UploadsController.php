<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SecurityPass;
use App\Models\Upload;
use Illuminate\Http\Request;

class UploadsController extends Controller
{

    /**
     * @var SecurityPass
     */
    private $securityPassModel;
    /**
     * @var Upload
     */
    private $uploadModel;

    /**
     * CategoriesController constructor.
     * @param SecurityPass $securityPassModel
     * @param Upload $uploadModel
     */
    public function __construct(SecurityPass $securityPassModel,Upload $uploadModel)
    {
        $this->securityPassModel = $securityPassModel;
        $this->uploadModel = $uploadModel;
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

    public function syncUploads(Request $request)
    {

        $this->validate($request,[
            'links' => 'required|array',
            'entity_type' => 'required',
            'entity_id' => 'required',
        ]);

        $uploads = [];

        foreach ($request->links as $link) {
            $upload = ['url' => $link, 'entity_type' => $request->entity_type, 'entity_id' => $request->entity_id];
            $this->uploadModel->create($upload);
        }

        return response()->json(['success'=>true]);

    }
}

