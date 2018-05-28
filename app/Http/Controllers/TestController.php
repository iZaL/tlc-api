<?php

namespace App\Http\Controllers;

use Illuminate\Cache\CacheManager;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Swap\Laravel\Facades\Swap;
use Torann\Currency\Currency;

class TestController extends BaseController
{

    public function index()
    {

        $rate = currency(12.00, 'USD', 'KWD',false);

        dd($rate);
    }
}
