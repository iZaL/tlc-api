<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Http\Resources\LoadsResource;
use App\Http\Resources\RoutesResource;
use App\Http\Resources\UserResource;
use App\Models\Job;
use App\Models\Load;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobsController extends Controller
{
    /**
     * @var Load
     */
    private $loadModel;
    /**
     * @var Job
     */
    private $jobModel;

    /**
     * LoadsController constructor.
     * @param Load $loadModel
     * @param Job $jobModel
     */
    public function __construct(Load $loadModel,Job $jobModel)
    {
        $this->loadModel = $loadModel;
        $this->jobModel = $jobModel;
    }

    public function getUpcomingJobs()
    {
        $driver = Auth::guard('api')->user()->driver;

        $now = Carbon::now();

        $jobs = $this->jobModel
//            ->with(['loads'=>function($q) use ($now) {
//                $q->whereDate('load_date','>',$now);
//            }])
//            ->where('driver_id',$driver->id)
//            ->get()
        ->all()
//            ->where
        ;

//        dd(Job::all()->toArray());

//        $driver->load([
//            'loads.origin.country',
//            'loads.destination.country',
//            'loads.trailer'
//        ]);

        return response()->json(['success'=>true,'data'=>$jobs]);
    }
//
//    public function getLoadDetails($loadID)
//    {
//        $load = $this->loadModel->with([
//            'origin.country',
//            'destination.country',
//            'loads.trailer'
//        ])->find($loadID);
//
//        return response()->json(['success'=>true,'data'=>new LoadsResource($load)]);
//
//    }

}