<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Http\Requests\Admin\UserValidationRequest;
use App\Http\Resources\Admin\UserResourceCollection;
use DataTables;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

        $this->middleware('permission:user-list', ['only' => ['list']]);
        $this->middleware('permission:user-create', ['only' => ['store']]);
        $this->middleware('permission:user-edit', ['only' => ['update']]);
        $this->middleware('permission:user-delete', ['only' => ['delete']]);
        $this->middleware('permission:user-show', ['only' => ['show']]);

    }

    public function list(Request $request){
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        // var_dump($orderby); die();
        $data = User::where('id','LIKE',"%{$search}%")
                    ->orWhere('name', 'LIKE',"%{$search}%")
                    ->orWhere('email', 'LIKE',"%{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(10);
        return new UserResourceCollection($data);
    }

    public function store(UserValidationRequest $request){
        $password = bcrypt($request->password);
        $request->merge(['password' => $password]);
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
