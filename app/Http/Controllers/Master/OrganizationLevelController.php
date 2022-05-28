<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\OrganizationLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-organization-level');
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
        $data = OrganizationLevel::with(
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->where($orderBy,'LIKE',"{$search}%")
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
            "code" => "required|unique:organization_levels,code",
            "name" => "required",
            "management" => "required",
            "back_date" => "required|integer",
            "data_order" => "required|integer"
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = OrganizationLevel::create($request->all());
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
        $data = OrganizationLevel::find($id);
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
            "code" => "required|unique:organization_levels,code,".$id,
            "name" => "required",
            "management" => "required",
            "back_date" => "required|integer",
            "data_order" => "required|integer"
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = OrganizationLevel::find($id);
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
            OrganizationLevel::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $data = OrganizationLevel::select('id','code','name')->get();
        return response()->json($data);
    }
}
