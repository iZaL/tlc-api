<?php

namespace App\Http\Controllers\Api\Shipper;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Shipper;
use App\Http\Resources\ShipperResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{

    private $shipperModel;
    /**
     * @var Employee
     */
    private $employee;

    /**
     * @param Shipper $shipperModel
     * @param Employee $employee
     */
    public function __construct(Shipper $shipperModel, Employee $employee)
    {
        $this->shipperModel = $shipperModel;
        $this->employee = $employee;
    }

    public function index()
    {
        $user = Auth::guard('api')->user();
        $shipper = $user->shipper;
        $shipper->load('employees');

        return (new ShipperResource($shipper))->additional([
            'success' => true,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        $shipper = $user->shipper;

        $employee = null;

        if($request->has('id')) {
            $employee = $this->employee->find($request->id);

            $validation = Validator::make($request->all(), [
                'name_en' => 'required|max:100',
                'name_ar' => 'required|max:100',
                'mobile' => 'required|unique:employees,mobile,'.$employee->id ,
                'phone' => 'required|unique:employees,phone,'.$employee->id ,
                'email' => 'required|unique:employees,email,'.$employee->id,
                'driver_interaction' => 'boolean'
            ]);
        } else {
            $validation = Validator::make($request->all(), [
                'name_en' => 'required|max:100',
                'name_ar' => 'required|max:100',
                'mobile' => 'required|unique:employees,mobile' ,
                'phone' => 'required|unique:employees,phone' ,
                'email' => 'required|unique:employees,email',
                'driver_interaction' => 'boolean'
            ]);
        }

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $data = [
            'mobile'             => $request->mobile,
            'phone'              => $request->phone,
            'email'              => $request->email,
            'name_en'            => $request->name_en,
            'name_ar'            => $request->name_ar,
            'driver_interaction' => $request->has('driver_interaction') ? $request->driver_interaction : false
        ];

        if($request->has('id')) {
             $employee->update($data);
        } else {
            $shipper->employees()->create();
        }

        $shipper->load('employees');

        return (new ShipperResource($shipper))->additional([
            'success' => true,
        ]);

    }
//    public function update(Request $request)
//    {
//        $user = Auth::guard('api')->user();
//        $shipper = $user->shipper;
//
//        $validation = Validator::make($request->all(), [
//            'mobile' => 'required|unique:shippers,mobile,' . $shipper->id,
//            'phone' => 'required|unique:shippers,phone,' . $shipper->id,
//            'email' => 'required|unique:shippers,email,' .$shipper->id,
//            'address_en' => 'max:255',
//            'address_ar' => 'max:255',
//            'user' => 'array',
//        ]);
//
//        if ($validation->fails()) {
//            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
//        }
//
//        $shipper->update([
//            'mobile' => $request->mobile,
//            'phone' => $request->phone,
//            'email' => $request->email,
//            'address_en' => $request->address_en,
//            'address_ar' => $request->address_ar,
//        ]);
//
//        $user->update($request->user);
//
//        return (new ShipperResource($shipper))->additional([
//            'success' => true,
//            'meta'    => [
//                'name_en'    => $user->name_en,
//                'name_ar'    => $user->name_ar,
//                'name_hi'    => $user->name_hi,
//                'address_en' => $shipper->address_en,
//                'address_ar' => $shipper->address_ar,
//                'address_hi' => $shipper->address_hi,
//            ]
//        ]);
//
//    }


}