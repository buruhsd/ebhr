<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display the all resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $data = Partner::with('npwp:id,name,number_npwp','postal_code:id,postal_code','village:id,district_id,name',
                'district:id,regency_id,name','regency:id,province_id,name','province:id,name')
            ->where('name','LIKE',"{$search}%")
            ->orderBy($orderBy, $sortBy)
            ->paginate(10);
        return response()->json($data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => "required",
            'agency' => "required",
            'address' => "required",
            'block' => "required",
            'no' => "required",
            'rt' => "required",
            'rw' => "required",
            'is_pkp' => "required",
            'npwp_id' => "required",
            'postal_code_id' => "required",
            'village_id' => "required",
            'district_id' => "required",
            'regency_id' => "required",
            'province_id' => "required",
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id(),'is_closed'=>false]);
        $data = Partner::create($request->all());
        return response()->json(['data'=>$data]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Partner::find($id);
        return response()->json(['data'=>$data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => "required",
            'agency' => "required",
            'address' => "required",
            'block' => "required",
            'no' => "required",
            'rt' => "required",
            'rw' => "required",
            'is_pkp' => "required",
            'is_closed' => "nullable",
            'npwp_id' => "required",
            'postal_code_id' => "required",
            'village_id' => "required",
            'district_id' => "required",
            'regency_id' => "required",
            'province_id' => "required",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Partner::find($id);
        $data->update($request->all());
        return response()->json(['data'=>$data]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = Partner::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getCode()
    {
        $number = Partner::register_number();
        return response()->json(['data' => $number]);
    }

    public function getData(Request $request)
    {
        $search = $request->search;
        return Partner::with('npwp:id,name,number_npwp','postal_code:id,postal_code','village:id,district_id,name',
        'district:id,regency_id,name','regency:id,province_id,name','province:id,name')
                ->where('name', 'LIKE',"%{$search}%")
                ->orWhere('code', 'LIKE',"%{$search}%")
                ->limit(10)->get();
    }
}
