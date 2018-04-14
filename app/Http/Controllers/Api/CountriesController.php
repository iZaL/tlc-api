<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\CountryResource;
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
     */
    public function getAll()
    {
        $countries = $this->countryModel->with(['loading_routes','locations'])->active()->get();
        return [
            'success' => true,
            'data'    => CountryResource::collection($countries)
        ];

    }

    public function getRoutesForCountry($countryID)
    {
        $country = $this->countryModel->with(['loading_routes'=>function($q){
            $q->active();
        }])->find($countryID);
        return response()->json(['success'=>true,'data'=>new CountryResource($country)]);

    }

}