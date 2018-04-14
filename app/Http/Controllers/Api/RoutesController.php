<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SecurityPassResource;
use App\Models\SecurityPass;

class RoutesController extends Controller
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

    public function getSecurityPasses()
    {
        $passes = $this->securityPassModel->with(['country'])->get();
        return response()->json(['success'=>true,'data'=>SecurityPassResource::collection($passes)]);
    }

}

