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
        $data = IdentityCard::with('religion:id,religion_name','work_type:id,code,type_name','marital_status:id,code,status_name','postal_code:id,postal_code','village:id,district_id,name','district:id,regency_id,name','regency:id,province_id,name','province:id,name')->where('id','LIKE',"%{$search}%")
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
            'gender' => 'required',
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
            'gender' => 'required',
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
        $identityCard->update($request->except('insertedBy'));

        return new IdentityCardResource($identityCard);
    }

    public function createEmployee(Request $request){
        $this->validate($request, [
            'tgl_surat' => 'required',
            'no_surat' => 'required',
            'no_induk' => 'required',
            'name_alias' => 'required',
            'identity_id' => 'required',
            'work_pattern_id' => 'required',
            'work_group_id' => 'required',
            'position_id' => 'required',
            'employee_status_id' => 'required',
            'development_status_id' => 'required',
            'start_date' => 'required',
            'description' => 'nullable',
            'insertedBy' => 'required',
            'updatedBy' => 'required',
        ]);
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
        return new IdentityCardResource($employee);
    }

    public function EmployeeUpdate(Request $request, Employee $employee){
        $this->validate($request, [
            'tgl_surat' => 'required',
            'no_surat' => 'required',
            'no_induk' => 'required',
            'name_alias' => 'required',
            'identity_id' => 'required',
            'work_pattern_id' => 'required',
            'work_group_id' => 'required',
            'position_id' => 'required',
            'employee_status_id' => 'required',
            'development_status_id' => 'required',
            'start_date' => 'required',
            'description' => 'nullable',
            'insertedBy' => 'required',
            'updatedBy' => 'required',
        ]);
        $employee->update($request->except('insertedBy'));

        return response()->json(['data' => $employee]);
    }

}
