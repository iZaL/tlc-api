<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryCollection;
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
        $countries = $this->countryModel->all();
        return new CountryCollection($countries);
    }



}