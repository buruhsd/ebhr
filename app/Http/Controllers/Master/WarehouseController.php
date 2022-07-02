<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Master\Warehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as ValidatonFactory;

class WarehouseController extends Controller
{
    public function __construct(ValidatonFactory $factory)
    {
        $this->middleware('auth:api');
        $factory->extend(
            'unik_name',
            function ($attribute, $value, $parameters) {
                $check = Warehouse::where(['name'=>$value,'branch_id'=>$parameters[0]])->first();
                if (is_null($check)){
                    return true;
                }
            },
            'Nama gudang sudah ada di cabang'
        );

        $this->middleware('permission:master-warehouse', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

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
        $data = Warehouse::with('branch',
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
            "branch_id" => "required",
            "code" => "required|unique:warehouses",
            "name" => "required|unik_name:".$request->branch_id,
            "description" => "required",
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Warehouse::create($request->all());
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
        $data = Warehouse::find($id);
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
            "branch_id" => "required",
            "name" => "required|unik_name:".$request->branch_id,
            "description" => "required",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Warehouse::find($id);
        $data->update($request->except(['code']));
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
            $data = Warehouse::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $data = Warehouse::select('id','code','name')->orderBy('name')->get();
        return response()->json(['data' => $data]);
    }
}
