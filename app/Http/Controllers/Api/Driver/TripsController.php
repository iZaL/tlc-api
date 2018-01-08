<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Load;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TripsController extends Controller
{


    private $tripModel;
    private $loadModel;

    public function __construct(Load $loadModel,Trip $tripModel)
    {
        $this->loadModel = $loadModel;
        $this->tripModel = $tripModel;
    }

    public function getUpcomingJobs()
    {
        $driver = Auth::guard('api')->user()->driver;

//        $now = Carbon::now();

        $jobs = Load::all();

        return response()->json(['success'=>true,'data'=>$jobs]);
    }
}