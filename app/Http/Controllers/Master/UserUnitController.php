<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\UserUnit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-user-unit');
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
        $data = UserUnit::with('insertedBy:id,name','updatedBy:id,name')->where('name','LIKE',"{$search}%")
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
            "code" => "required|string|max:10|unique:user_units,code",
            "name" => "required",
            "description" => "required",
        ]);
        $request->merge(['code'=>strtoupper($request->code),'insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = UserUnit::create($request->all());
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
        $data = UserUnit::with('insertedBy:id,name','updatedBy:id,name')->find($id);
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
            "code" => "required|string|max:10|unique:user_units,code,".$id,
            "name" => "required",
            "description" => "required",
        ]);
        $request->merge(['code'=>strtoupper($request->code),'updatedBy'=>Auth::id()]);
        $data = UserUnit::find($id);
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
            $data = UserUnit::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(){
        $data = UserUnit::orderBy('name')->get();
        return response()->json(['data' => $data]);
    }
}
