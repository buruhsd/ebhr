<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Npwp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NpwpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-npwp', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
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
        $data = Npwp::with('postal_code:id,postal_code',
            'village:id,district_id,name','district:id,regency_id,name',
            'regency:id,province_id,name','province:id,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->where('number_npwp','LIKE',"{$search}%")
            ->orWhere('name','LIKE',"{$search}%")
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
            'number_npwp' => "required|numeric|unique:npwps,number_npwp|digits:15",
            'name' => "required",
            'phone_number' => "nullable",
            'address' => "required",
            'block' => "nullable",
            'no' => "nullable",
            'rt' => "nullable",
            'rw' => "nullable",
            'letter_date' => "required|date",
            'postal_code_id' => "required",
            'village_id' => "required",
            'district_id' => "required",
            'regency_id' => "required",
            'province_id' => "required",
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Npwp::create($request->all());
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
        $data = Npwp::find($id);
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
            'number_npwp' => "required|numeric|digits:15",
            'name' => "required",
            'phone_number' => "nullable",
            'address' => "required",
            'block' => "nullable",
            'no' => "nullable",
            'rt' => "nullable",
            'rw' => "nullable",
            'letter_date' => "required|date",
            'postal_code_id' => "required",
            'village_id' => "required",
            'district_id' => "required",
            'regency_id' => "required",
            'province_id' => "required",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Npwp::find($id);
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
            $data = Npwp::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $search = $request->search;
        return Npwp::select('id','name','number_npwp')
                ->where('name', 'LIKE',"{$search}%")
                ->orWhere('number_npwp', 'LIKE',"{$search}%")->limit(10)->get();
    }
}
