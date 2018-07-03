<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Customer;
use App\Http\Resources\CustomerResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{

    private $customerModel;
    /**
     * @var Employee
     */
    private $employee;

    /**
     * @param Customer $customerModel
     * @param Employee $employee
     */
    public function __construct(Customer $customerModel, Employee $employee)
    {
        $this->customerModel = $customerModel;
        $this->employee = $employee;
    }

    public function index()
    {
        $user = Auth::guard('api')->user();
        $customer = $user->customer;
        $customer->load('employees');

        return (new CustomerResource($customer))->additional([
            'success' => true,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        $customer = $user->customer;

        $employee = null;

        if($request->has('id')) {
            $employee = $this->employee->find($request->id);

            $validation = Validator::make($request->all(), [
                'name' => 'required|max:100',
//                'name_ar' => 'required|max:100',
                'mobile' => 'required|unique:employees,mobile,'.$employee->id ,
                'phone' => 'required|unique:employees,phone,'.$employee->id ,
                'email' => 'required|unique:employees,email,'.$employee->id,
                'driver_interaction' => 'boolean'
            ]);
        } else {
            $validation = Validator::make($request->all(), [
//                'name_en' => 'required|max:100',
                'name' => 'required|max:100',
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
            'name_en'            => $request->name,
            'name_ar'            => $request->name,
            'name_hi'            => $request->name,
            'driver_interaction' => $request->has('driver_interaction') ? $request->driver_interaction : false
        ];

        if($request->has('id')) {
             $employee->update($data);
        } else {
            $customer->employees()->create($data);
        }

        $customer->load('employees');

        return (new CustomerResource($customer))->additional([
            'success' => true,
        ]);

    }
//    public function update(Request $request)
//    {
//        $user = Auth::guard('api')->user();
//        $customer = $user->customer;
//
//        $validation = Validator::make($request->all(), [
//            'mobile' => 'required|unique:customers,mobile,' . $customer->id,
//            'phone' => 'required|unique:customers,phone,' . $customer->id,
//            'email' => 'required|unique:customers,email,' .$customer->id,
//            'address_en' => 'max:255',
//            'address_ar' => 'max:255',
//            'user' => 'array',
//        ]);
//
//        if ($validation->fails()) {
//            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
//        }
//
//        $customer->update([
//            'mobile' => $request->mobile,
//            'phone' => $request->phone,
//            'email' => $request->email,
//            'address_en' => $request->address_en,
//            'address_ar' => $request->address_ar,
//        ]);
//
//        $user->update($request->user);
//
//        return (new CustomerResource($customer))->additional([
//            'success' => true,
//            'meta'    => [
//                'name_en'    => $user->name_en,
//                'name_ar'    => $user->name_ar,
//                'name_hi'    => $user->name_hi,
//                'address_en' => $customer->address_en,
//                'address_ar' => $customer->address_ar,
//                'address_hi' => $customer->address_hi,
//            ]
//        ]);
//
//    }


}