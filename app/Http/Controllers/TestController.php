<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateCordinateDistance;
use App\Models\Load;
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
        $coordinates = [[48.8234055, 2.3072664],[43.296482, 5.36978]];

        $load = Load::find(1);

        $distance = CalculateCordinateDistance::dispatch($load);

    }
}
