<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

//    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * @var User
     */
    private $userModel;

    /**
     * Create a new controller instance.
     *
     * @param User $userModel
     */
    public function __construct(User $userModel)
    {
        $this->middleware('guest');
        $this->userModel = $userModel;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name_en' => 'required|max:50',
            'name_ar' => 'required|max:50',
            'email' => 'required|email|max:50|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = $this->userModel->create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json(['data'=>$user,'success'=>true]);
    }
}
