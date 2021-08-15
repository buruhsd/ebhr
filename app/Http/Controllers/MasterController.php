<?php

namespace App\Http\Controllers;

use App\Models\Master\Unit;
use App\Models\Master\ProductCategory;
use App\Models\Master\Products;
use App\Models\Master\Warehouse;
use App\Models\Rank;
use App\Models\Regency;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Religion;
use App\Models\WorkType;
use App\Models\WorkGroup;
use App\Models\WorkPattern;
use App\Models\EmployeeStatus;
use App\Models\DevelopmentStatus;
use App\Models\Organization;
use App\Models\PostalCode;
use App\Models\WorkStatus;
use App\Models\PointOfHire;
use Illuminate\Http\Request;
use App\Models\MaritalStatus;
use App\Models\Purchase\TransactionType;
use App\Models\Purchase\PurchaseCategory;
use App\Models\Purchase\PurchaseUrgentity;
use App\Models\Purchase\PurchaseNecessary;

class MasterController extends Controller
{
    public function products(){
        $data = Products::get();
        return response()->json(['data' => $data]);
    }

    public function getNumberProducts()
    {
        $number = Products::register_number();
        return response()->json(['data' => $number]);
    }
    public function unit(){
        $data = Unit::get();
        return response()->json(['data' => $data]);
    }
    public function warehouse($id){
        $data = Warehouse::select('id','code','name')->where('branch_id',$id)->get();
        return response()->json(['data' => $data]);
    }
    public function product_category(){
        $data = ProductCategory::whereNotNull('parent_id')->get();
        return response()->json(['data' => $data]);
    }

    public function position(){
        $data = Position::get();
        return response()->json(['data' => $data]);
    }

    public function workGroup(){
        $data = WorkGroup::get();
        return response()->json(['data' => $data]);
    }

    public function workPattern(){
        $data = WorkPattern::get();
        return response()->json(['data' => $data]);
    }

    public function employeeStatus(){
        $data = EmployeeStatus::get();
        return response()->json(['data' => $data]);
    }

    public function developmentStatus(){
        $data = DevelopmentStatus::get();
        return response()->json(['data' => $data]);
    }

    public function religionList(){
        $data = Religion::get();
        return response()->json(['data' => $data]);
    }

    public function workStatus(Request $request){
        $data = WorkStatus::select('id','code','name')->get();
        return response()->json(['data' => $data]);
    }

    public function pointHire(){
        $data = PointOfHire::get();
        return response()->json(['data' => $data]);
    }

    public function rank(){
        $data = Rank::get();
        return response()->json(['data' => $data]);
    }

    public function organization(){
        $data = Organization::get();
        return response()->json(['data' => $data]);
    }

    public function maritalStatusList(){
        $data = MaritalStatus::get();
        return response()->json(['data' => $data]);
    }

    public function workType(){
        $data = WorkType::get();
        return response()->json(['data' => $data]);
    }

    public function postalCodeList(Request $request){
        $q = $request->q;
        $d = $request->d;
        $data = PostalCode::select('id','postal_code')
            ->where('district_name',$d)
            ->when($q, function ($query) use ($q){
                $query->where('village_name',$q);
            })
            ->groupBy('postal_code')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function villageList($id){
        $data = Village::where('district_id', $id)->get();

        return response()->json(['data' => $data]);
    }

    public function searchRegency(Request $request){
        $search = $request->q;
        $data = Regency::select('id','name')
            ->where('name','like','%'.$search.'%')->limit(20)->get();
        return response()->json(['data' => $data]);
    }

    public function districtList(Request $request){

        $kode = $request->kode;
        $id  = $request->id;
        if($id != null){
            $data = District::where('regency_id', $id)->get();
        }elseif($kode != null){
            $data = District::where('id', $kode)->get();
        }else{
            $data = District::get();
        }

        return response()->json(['data' => $data]);
    }

    public function regencyList(Request $request){

        $kode = $request->kode;
        $id  = $request->id;
        if($id != null){
            $data = Regency::where('province_id', $id)->get();
        }elseif($kode != null){
            $data = Regency::where('id', $kode)->get();
        }else{
            $data = Regency::get();
        }

        return response()->json(['data' => $data]);
    }

    public function provinceList(Request $request){
        $kode = $request->kode;
        if($kode == null){
            $data = Province::get();
        }else{
            $data = Province::where('id', $kode)->get();
        }


        return response()->json(['data' => $data]);
    }

    public function detailNik(Request $request){
        $nik = $request->code;
        if(is_null($nik)){
            return response()->json([
                'success' => false,
            ]);
        }
        $province_id = substr($nik,0,2);
        $province = Province::select('id','name')->where('id', $province_id)->first();
        $regency_id = substr($nik,0,4);
        $regency = Regency::select('id','name')->where('id', $regency_id)->first();
        $district_id = substr($nik,0,6);
        $district = District::select('id','name')->where('id', $district_id)->first();
        $date = substr($nik,6,2);
        $gender = null;
        if(strlen($nik) >= 8){
            $gender = 'laki-laki';
            if($date >= 40){
                $gender = 'perempuan';
            }
        }
        return response()->json([
            'success' => true,
            'nik' => $nik,
            'province' => $province,
            'regency' => $regency,
            'district' => $district,
            'gender' => $gender
        ]);
    }

    // Purchase
    public function branch(){
        $data = Branch::select('id','name')->get();
        return response()->json(['data' => $data]);
    }

    public function transactionType(){
        $data = TransactionType::select('id','name')->get();
        return response()->json(['data' => $data]);
    }

    public function purchaseCategory(){
        $data = PurchaseCategory::select('id','name')->get();
        return response()->json(['data' => $data]);
    }

    public function purchaseUrgentity(){
        $data = PurchaseUrgentity::select('id','name')->get();
        return response()->json(['data' => $data]);
    }

    public function purchaseNecessary(){
        $data = PurchaseNecessary::select('id','name')->get();
        return response()->json(['data' => $data]);
    }

}
