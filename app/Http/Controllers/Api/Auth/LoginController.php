<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pushtoken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * @var User
     */
    private $userRepo;
    private $pushtokenModel;

    /**
     * LoginController constructor.
     * @param User $userRepo
     * @param Pushtoken $pushtokenModel
     */
    public function __construct(User $userRepo,Pushtoken $pushtokenModel)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->userRepo = $userRepo;
        $this->pushtokenModel = $pushtokenModel;
    }

    /*
     * @POST
     */
    public function login(Request $request)
    {

        if($request->has('api_token')) {
            return $this->loginViaToken($request);
        }

        $validator = $this->customValidate($request,[
            'email' => 'email|required',
            'password' => 'required',
        ]);

        if(is_array($validator)) {
            return response()->json($validator);
        }

        $email = strtolower($request->email);
        $password = $request->password;

        $loggedIn = Auth::attempt(['email'=>$email,'password'=>$password,'active'=>true]);

        if(!$loggedIn) {
            return response()->json([
                'success'=>false,
                'message'=> __('Wrong Credentials')
            ]);
        }

        $user = Auth::user();

        $user->makeVisible('api_token');

        if($request->pushtoken) {

            $pushToken = $this->pushtokenModel->where('token',$request->pushtoken)->first();

            if($pushToken && $pushToken->user_id != $user->id) {
                $pushToken->user_id = $user->id;
                $pushToken->save();
            }

        }


        return response()->json(['success'=>true,'data'=>$user]);
    }

    /*
     * @POST
     */
    public function register(Request $request)
    {
        $validator = $this->customValidate($request,[
            'name_en' => 'required|max:50',
            'name_ar' => 'required|max:50',
            'email' => 'email|required|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'mobile' => 'required',
        ]);

        if(is_array($validator)) {
            return response()->json($validator);
        }

        $name_en = $request->name_en;
        $name_ar = $request->name_ar;
        $email = strtolower($request->email);
        $password = bcrypt($request->password);
        $mobile = $request->mobile;
        $api_token = str_random(20);
        $active = true;
        $isCompany = $request->has('isCompany') ? $request->isCompany : false;
        $company = ['description' => '' , 'phone' => '' ,'address' => ''];

        try {
            $user = $this->userRepo->create(
                compact('name_en','name_ar','email','password','mobile','active','api_token','isCompany','company'));
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>'Could not create user']);
        }

        return response()->json(['success'=>true,'data'=>$user]);
    }

    /*
     * @GET
     */
    public function logout(Request $request)
    {
        return response()->json(['success'=>true,'message'=>'logged out']);
    }

    private function loginViaToken($request)
    {
        $user = Auth::guard('api')->user();

        if(!$user) {
            return response()->json('wrong token');
        }

        $user->makeVisible('api_token');

        if($request->pushtoken) {
            $pushToken = $this->pushtokenModel->where('token',$request->pushtoken)->first();
            if($pushToken && $pushToken->user_id != $user->id) {
                $pushToken->user_id = $user->id;
                $pushToken->save();
            }
        }

        return response()->json(['success'=>true,'data'=>$user]);
    }


    // Forgot Password
    // Send Confirmation Code
    public function forgotPassword(Request $request)
    {

        $validator = $this->customValidate($request,[
            'email' => 'email|required',
        ]);

        if(is_array($validator)) {
            return response()->json($validator);
        }
        // generate confirmation code
        // save in DB
        $email = strtolower($request->email);

        $user = User::where('email',$email)->first();

        if(!$user) {
            return response()->json(['success'=>false,'message'=>'email address not found']);
        }

//        $genCode = str_random(6);
        $genCode = '123456';

        $user->forgot_password_code = $genCode;
        $user->save();

        // send email

        return response()->json(['success'=>true,'data'=>$user]);

    }

    // Send Confirmation Code
    public function recoverPassword(Request $request)
    {

        $validator = $this->customValidate($request,[
            'email' => 'email|required',
        ]);

        if(is_array($validator)) {
            return response()->json($validator);
        }

        // generate confirmation code
        // save in DB
        $email = strtolower($request->email);
        $code = $request->confirmation_code;

        $user = User::where('email',$email)->first();

        if(!$user) {
            return response()->json(['success'=>false,'message'=>'Unknown User']);
        }

        if($code !== $user->forgot_password_code) {
            return response()->json(['success'=>false,'message'=>'Invalid Code']);
        }

        return response()->json(['success'=>true,'message'=>'User Can Change Password']);

    }

    public function updatePassword(Request $request)
    {
        // generate confirmation code
        // save in DB
        $validator = $this->customValidate($request,[
            'email' => 'email|required',
        ]);

        if(is_array($validator)) {
            return response()->json($validator);
        }

        $email = strtolower($request->email);
        $password = $request->password;

        $this->validate($request,[
            'email' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('email',$email)->first();

        if(!$user) {
            return response()->json(['success'=>false,'message'=>'Unknown User']);
        }

        $user->password = bcrypt($password);
        $user->save();

        return response()->json(['success'=>true,'message'=>'User Can Change Password']);

    }


}
