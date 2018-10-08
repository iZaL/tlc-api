<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PushToken;
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
    private $pushTokenModel;

    /**
     * LoginController constructor.
     * @param User $userRepo
     * @param PushToken $pushTokenModel
     */
    public function __construct(User $userRepo, PushToken $pushTokenModel)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->userRepo = $userRepo;
        $this->pushTokenModel = $pushTokenModel;
    }

    /*
     * @POST
     */
    public function login(Request $request)
    {

        if (Auth::guard('api')->user()) {
            return $this->loginViaToken($request);
        }

        $validation = Validator::make($request->all(), [
            'email'    => 'email|required',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $email = strtolower($request->email);
        $password = $request->password;

        $loggedIn = Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1]);

        if (!$loggedIn) {
            return response()->json([
                'success' => false,
                'message' => trans('wrong_credentials')
            ]);
        }

        $user = Auth::user();

        if ($request->push_token) {

            $pushToken = $this->pushTokenModel->where('token', $request->push_token)->first();

            if ($pushToken && $pushToken->user_id != $user->id) {
                $pushToken->user_id = $user->id;
                $pushToken->save();
            }

        }

//        return response()->json(['success'=>true,''])

        return (new UserResource($user))->additional([
            'success' => true,
            'meta'    => [
                'api_token' => $user->api_token,
            ]]);

    }

    /*
     * @POST
     */
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name_en'  => 'required|max:50',
            'name_ar'  => 'max:50',
            'name_hi'  => 'max:50',
            'email'    => 'email|required|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'mobile'   => 'required|unique:users,mobile',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $nameEn = $request->name_en;
        $nameAr = $request->name_ar;
        $nameHi = $request->name_hi;
        $email = strtolower($request->email);
        $password = bcrypt($request->password);
        $mobile = $request->mobile;
        $apiToken = str_random(16);
        $otp = rand(1000, 9999);
        $isCustomer = $request->has('isCustomer') ? $request->isCustomer : false;

        try {
            $user = $this->userRepo->create([
                'name_en'   => $nameEn,
                'name_ar'   => $nameAr,
                'name_hi'   => $nameHi,
                'email'     => $email,
                'password'  => $password,
                'mobile'    => $mobile,
                'api_token' => $apiToken,
                'otp'       => $otp,

            ]);

            if ($isCustomer) {
                $user->customer()->create();
            } else {
                $user->driver()->create();
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => trans('general.registration_failed')]);
        }

        return new UserResource($user);
    }

    /*
     * @GET
     */
    public function logout(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'logged out']);
    }

    private function loginViaToken($request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json('wrong token');
        }

        $user->makeVisible('api_token');

        if ($request->push_token) {
            $pushToken = $this->pushTokenModel->where('token', $request->push_token)->first();
            if ($pushToken && $pushToken->user_id != $user->id) {
                $pushToken->user_id = $user->id;
                $pushToken->save();
            }
        }

        return response()->json(['success'=>true,'data'=>new UserResource($user)]);
    }


    // Forgot Password
    // Send Confirmation Code
    public function forgotPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'email|required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }
        // generate confirmation code
        // save in DB
        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'email address not found']);
        }

//        $genCode = str_random(6);
        $genCode = '123456';

        $user->forgot_password_code = $genCode;
        $user->save();

        // send email

        return response()->json(['success' => true]);

    }

    // Send Confirmation Code
    public function recoverPassword(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'email' => 'email|required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        // generate confirmation code
        // save in DB
        $email = strtolower($request->email);
        $code = $request->confirmation_code;

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unknown User']);
        }

        if ($code !== $user->forgot_password_code) {
            return response()->json(['success' => false, 'message' => 'Invalid Code']);
        }

        return response()->json(['success' => true, 'message' => 'User Can Change Password']);

    }

    public function updatePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'email|required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $email = strtolower($request->email);
        $password = $request->password;

        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unknown User']);
        }

        $user->password = bcrypt($password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'User Can Change Password']);

    }

    public function confirmOTP(Request $request)
    {
        // generate confirmation code
        // save in DB
        $validation = Validator::make($request->all(), [
            'code'   => 'required',
            'mobile' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => trans('general.invalid_user')]);
        }

        if ($user->otp == $request->code) {
            if (!$user->isActive()) {
                $this->activateUser($user);
                return response()->json(['success' => true, 'data' => $user]);
            }
            return response()->json(['success' => false, 'message' => trans('general.account_already_active')]);
        }

        return response()->json(['success' => false, 'message' => trans('general.invalid_otp')]);

    }

    public function activateUser($user)
    {
        $user->active = 1;
        $user->save();

        // send email,sms

        return $user;
    }


}
