<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IdentityCard;
use App\Models\Employee;
use App\Http\Resources\Hr\IdentityCardResource;
use App\Http\Resources\Hr\IdentityCardResourceCollection;

class EmployeeController extends Controller
{
    public function IdentityCardList(Request $request)
    {
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        // var_dump($orderby); die();
        $data = IdentityCard::where('id','LIKE',"%{$search}%")
                    ->orWhere('name', 'LIKE',"%{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(10);
        return new IdentityCardResourceCollection($data);
    }

    public function IdentityCardStore(Request $request){
        $data = IdentityCard::create($request->all());

        return new IdentityCardResource($data);
    }

    public function IdentityCardUpdate(Request $request, IdentityCard $identityCard){
        $data = $identityCard->update($request->all());

        return new IdentityCardResource($data);
    }
}
