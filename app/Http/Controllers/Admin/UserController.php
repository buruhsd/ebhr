<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResourceCollection;


class UserController extends Controller
{
    public function list(Request $request){
        //
        // return User::paginate(10);
        $search = $request->search;
        $data = User::search($search)->paginate(10);
        return new UserResourceCollection($data);
    }
}
