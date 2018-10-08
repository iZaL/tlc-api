<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
{
    /**
     * @var Customer
     */
    private $customerModel;

    /**
     * @param Customer $customerModel
     */
    public function __construct(Customer $customerModel)
    {
        $this->customerModel = $customerModel;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $customers = $this->customerModel->all();
        $customerIDs = range(1,$customers->count());

        $title = 'Customers';

        return view('customers.index', compact('customers','title','customers','customerIDs','breadcrumbs'));
    }

    public function show($id)
    {
        $customer = $this->customerModel->find($id);
        $customers = $this->customerModel->pluck('name_en','id');
        $title = $customer->title;
        $packageIDs = range(1,$customer->packages->count());

        return view('admin.customers.view',compact('title','customer','customers','packageIDs'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name_en' => 'required',
            'name_ar' => 'required',
            'image' => 'required|image'
        ]);

        $customer =$this->customerModel->create($request->all());

        if($request->hasFile('image')) {
            try {
                $image = $this->uploadImage($request->image);
                $customer->image = $image;
                $customer->save();
            } catch (\Exception $e) {
                $customer->delete();
                redirect()->back()->with('success','Customers Could not be saved because the image failed to Upload');
            }
        }

        return redirect()->back()->with('success','Customer Saved');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name_en' => 'required',
            'name_ar' => 'required',
            'image' => 'image'
        ]);

        $customer = $this->customerModel->find($id);
        $customer->update($request->all());

        if($request->hasFile('image')) {
            try {
                $image = $this->uploadImage($request->image);
                $customer->image = $image;
                $customer->save();
            } catch (\Exception $e) {
                $customer->delete();
                redirect()->back()->with('success','Services Could not be saved because The Image failed to Upload');
            }
        }

        return redirect()->back()->with('success','Customer Updated');
    }

    public function destroy($id)
    {
        $customer = $this->customerModel->find($id);
        $customer->delete();
        return redirect()->back()->with('success','Customer Deleted');
    }

    public function reOrganize(Request $request)
    {
        $customer  = $this->customerModel->find($request->id);
        $customer->order = $request->order;
        $customer->save();
        return response()->json(['success'=>true,'id' => $request->id,'order' => $request->order]);
    }

}
