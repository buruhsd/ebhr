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
                    ->paginate(20);
        return new IdentityCardResourceCollection($data);
    }

    public function IdentityCardStore(Request $request){
        $data = IdentityCard::create($request->all());

        return new IdentityCardResource($data);
    }

    public function showIdentityCard(IdentityCard $identityCard){
        return new IdentityCardResource($identityCard);
    }

    public function IdentityCardUpdate(Request $request, IdentityCard $identityCard){
        $data = $identityCard->update($request->all());

        return new IdentityCardResource($data);
    }

    public function createEmployee(Request $request){
        $data = Employee::create($request->all());

        return response()->json(['data' => $data]);
    }

    public function EmployeeList(Request $request){
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        // var_dump($orderby); die();
        $data = Employee::where('id','LIKE',"%{$search}%")
                    ->orWhere('name_alias', 'LIKE',"%{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(20);
        return new IdentityCardResourceCollection($data);
    }

    public function showEmployee(Employee $employee){
        return new IdentityCardResource($identityCard);
    }

    public function EmployeeUpdate(Request $request, Employee $employee){
        $data = $employee::update($request->all());

        return response()->json(['data' => $data]);
    }

}
