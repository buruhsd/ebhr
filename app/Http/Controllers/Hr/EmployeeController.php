<?php

namespace App\Http\Controllers\Hr;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IdentityCard;
use App\Models\Employee;
use App\Http\Resources\Hr\IdentityCardResource;
use App\Http\Resources\Hr\IdentityCardResourceCollection;

class EmployeeController extends Controller
{
    public function searchIdentityCard(Request $request)
    {
        $search = $request->q;
        $data = IdentityCard::select('id','nik','name')
                    ->where('nik','LIKE',"{$search}%")
                    ->orWhere('name', 'LIKE',"%{$search}%")
                    ->limit(20)
                    ->get();
        return response()->json(['data' => $data]);
    }

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
        $data = IdentityCard::with('religion:id,religion_name','work_type:id,code,name','marital_status:id,code,status_name','postal_code:id,postal_code','village:id,district_id,name','district:id,regency_id,name','regency:id,province_id,name','province:id,name')
                    ->where('nik','LIKE',"{$search}%")
                    ->orWhere('name', 'LIKE',"{$search}%")
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
            'description' => 'nullable'
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
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
            'description' => 'nullable'
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $identityCard->update($request->all());

        return new IdentityCardResource($identityCard);
    }

    public function createEmployee(Request $request){
        $this->validate($request, [
            'branch_id' => 'required',
            'tgl_surat' => 'required',
            'name_alias' => 'required',
            'identity_id' => 'required',
            'rank_id' => 'required',
            'organization_id' => 'required',
            'point_hire_id' => 'required',
            'work_pattern_id' => 'required',
            'work_group_id' => 'required',
            'work_type_id' => 'required',
            'position_id' => 'required',
            'employee_status_id' => 'required',
            'development_status_id' => 'required',
            'start_date' => 'required',
            'description' => 'nullable',
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
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
        $data = Employee::with('identity:id,nik,name','work_pattern:id,name','work_group:id,name',
            'position:id,name','employee_status:id,name','development_status:id,name','branch:id,name',
            'work_type:id,type_name','rank:id,name','hire:id,name','organization:id,name')
                    ->where('no_induk','LIKE',"{$search}%")
                    ->orWhere('name_alias', 'LIKE',"{$search}%")
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
            'name_alias' => 'required',
            'identity_id' => 'required',
            'rank_id' => 'required',
            'organization_id' => 'required',
            'point_hire_id' => 'required',
            'work_pattern_id' => 'required',
            'work_group_id' => 'required',
            'work_type_id' => 'required',
            'position_id' => 'required',
            'employee_status_id' => 'required',
            'development_status_id' => 'required',
            'start_date' => 'required',
            'description' => 'nullable'
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $employee->update($request->except(['no_induk','no_surat']));

        return response()->json(['data' => $employee]);
    }

    public function getNoInduk(Request $request)
    {
        $branchId = $request->branch;
        $typeId = $request->type;
        $no = Employee::numberInduk($branchId,$typeId);
        return response()->json(['data' => $no]);
    }

    public function getNoSurat(Request $request)
    {
        $branchId = $request->id;
        $no = Employee::numberSurat($branchId);
        return response()->json(['data' => $no]);
    }

}
