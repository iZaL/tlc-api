<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountriesController extends Controller
{
    /**
     * @var Country
     */
    private $countryModel;

    /**
     * CountriesController constructor.
     * @param Country $countryModel
     */
    public function __construct(Country $countryModel)
    {
        $this->countryModel = $countryModel;
    }

    /**
     * @param Request $request
     * Get loads for the Authenticated Driver
     * //@todo: Cache Query
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $user = Auth::guard('api')->user();

        $countries = $this->countryModel->all();

        return response()->json(['success' => true, 'data' => $countries]);
    }



}