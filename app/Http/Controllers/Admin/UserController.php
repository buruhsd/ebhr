<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Http\Requests\Admin\UserValidationRequest;
use App\Http\Resources\Admin\UserResourceCollection;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function list(Request $request){

        $search = $request->search;
        $data = User::search($search)->paginate(10);
        return new UserResourceCollection($data);
    }

    public function store(UserValidationRequest $request){
        $data = User::create($request->all());
        return new UserResource($data);
    }

    public function update(Request $request, User $user){
        $data = $user->update($request->all());

        return new UserResource($user);
    }

    public function show(User $user){
        return new UserResource($user);
    }

    public function delete(User $user){
        $user->delete();
        return response()->json(['data' => 'data deleted']);
    }
}
