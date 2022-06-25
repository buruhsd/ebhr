<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\CustomerGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-supplier-category');
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
        $data = CustomerGroup::with('parent',
            'insertedBy:id,name',
            'updatedBy:id,name')
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
            "code" => "required",
            "name" => "required",
            "parent_id" => "nullable",
        ]);
        try {
            $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
            $data = CustomerGroup::create($request->all());
            return response()->json(['data'=>$data]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json([
                    "message" => "The given data was invalid.",
                    'errors'=> [
                        'code' => ['Kode sudah digunakan']
                    ]
                ],422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = CustomerGroup::find($id);
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
            "code" => "required|unique:customer_groups,code,".$id,
            "name" => "required",
            "parent_id" => "nullable",
        ]);

        try {
            $request->merge(['updatedBy'=>Auth::id()]);
            $data = CustomerGroup::find($id);
            $data->update($request->except(['insertName']));
            return response()->json(['data'=>$data]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json([
                    "message" => "The given data was invalid.",
                    'errors'=> [
                        'code' => ['Kode sudah digunakan']
                    ]
                ],422);
            }
        }
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
            $data = CustomerGroup::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $data = CustomerGroup::get();
        return response()->json(['data' => $data]);
    }

    public function getParent(Request $request)
    {
        $data = CustomerGroup::whereNull('parent_id')->get();
        return response()->json(['data' => $data]);
    }

    public function getChilds(Request $request)
    {
        $data = CustomerGroup::whereNotNull('parent_id')->get();
        return response()->json(['data' => $data]);
    }
}
