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
        if(is_null($orderBy)){
            $orderBy = 'name';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = IdentityCard::where('id','LIKE',"%{$search}%")
                    ->orWhere('name', 'LIKE',"%{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(20);
        return new IdentityCardResourceCollection($data);
    }

    public function IdentityCardStore(Request $request){
        $this->validate($request, [
            'nik' => 'required|numeric|digits:16',
            'name' => 'required',
            'date_of_birth' => 'required',
            'blood_type' => 'required',
            'religion_id' => 'required',
            'work_type_id' => 'required',
            'nationality' => 'required',
            'marital_status_id' => 'required',
            'address' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'postal_code_id' => 'required',
            'village_id' => 'required',
            'district_id' => 'required',
            'regency_id' => 'required',
            'province_id' => 'required',
            'published_date_ktp' => 'required',
            'description' => 'nullable',
            'insertedBy' => 'required',
            'updatedBy' => 'required',
        ]);
        $data = IdentityCard::create($request->all());

        return new IdentityCardResource($data);
    }

    public function showIdentityCard(IdentityCard $identityCard){
        return new IdentityCardResource($identityCard);
    }

    public function IdentityCardUpdate(Request $request, IdentityCard $identityCard){
        $this->validate($request, [
            'nik' => 'required|numeric|digits:16',
            'name' => 'required',
            'date_of_birth' => 'required',
            'blood_type' => 'required',
            'religion_id' => 'required',
            'work_type_id' => 'required',
            'nationality' => 'required',
            'marital_status_id' => 'required',
            'address' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'postal_code_id' => 'required',
            'village_id' => 'required',
            'district_id' => 'required',
            'regency_id' => 'required',
            'province_id' => 'required',
            'published_date_ktp' => 'required',
            'description' => 'nullable',
            'insertedBy' => 'required',
            'updatedBy' => 'required',
        ]);
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
        if(is_null($orderBy)){
            $orderBy = 'name_alias';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
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
