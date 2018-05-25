<?php


namespace App\Http\Controllers\Api\Driver;

use App\Events\DriversLocationUpdated;
use App\Events\DriverStartedJob;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentTypeResource;
use App\Managers\LoadManager;
use App\Managers\TripManager;
use App\Http\Resources\LoadResource;
use App\Http\Resources\TripResource;
use App\Jobs\SendPushNotificationsToAllDevice;
use App\Models\DocumentType;
use App\Models\Trip;
use App\Models\Load;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentsController extends Controller
{

    /**
     * @var DocumentType
     */
    private $documentTypeModel;

    /**
     * DocumentsController constructor.
     * @param DocumentType $documentTypeModel
     */
    public function __construct(DocumentType $documentTypeModel)
    {
        $this->documentTypeModel = $documentTypeModel;
    }

    public function getTypes()
    {

        $documentTypes = $this->documentTypeModel->all();

        return response()->json(['success'=>true,'data'=>DocumentTypeResource::collection($documentTypes)]);

    }


}