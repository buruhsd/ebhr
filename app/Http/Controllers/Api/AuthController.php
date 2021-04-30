<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Admin\UserResource;
use App\Http\Requests\ValidateUserRegistration;
use App\Http\Requests\ValidateUserLogin;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(ValidateUserRegistration $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return new UserResource($user);
    }

    public function login(ValidateUserLogin $request){

        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return  response()->json([
                'type' =>'failed',
                'errors' => [
                    'message' => ['Incorrect email or password.']
                ]
            ]);
        }

        return response()->json([
            'type' =>'success',
            'message' => 'Logged in.',
            'token' => $token
        ]);
    }

    public function user()
    {
       return new UserResource(auth()->user());
    }

    public function logout() {
        auth()->logout();

        return response()->json(['success' => true,'data' => 'User successfully signed out']);
    }

    protected function createNewToken($token, $success = false){
        return response()->json([
            'success' => $success,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }


}
