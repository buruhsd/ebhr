<?php

namespace App\Http\Controllers;

use App\Models\Regency;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use App\Models\Religion;
use App\Models\WorkType;
use App\Models\PostalCode;
use Illuminate\Http\Request;
use App\Models\MaritalStatus;

class MasterController extends Controller
{
    public function religionList(){
        $data = Religion::get();
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

    public function postalCodeList(){
        $data = PostalCode::get();

        return response()->json(['data' => $data]);
    }

    public function villageList($id){
        $data = Village::where('district_id', $id)->get();

        return response()->json(['data' => $data]);
    }

    public function districtList($id){
        $data = District::where('regency_id', $id)->get();

        return response()->json(['data' => $data]);
    }

    public function regencyList($id){
        $data = Regency::where('province_id', $id)->get();

        return response()->json(['data' => $data]);
    }

    public function provinceList(){
        $data = Province::get();

        return response()->json(['data' => $data]);
    }
}
