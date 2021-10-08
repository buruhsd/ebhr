<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Master\ProductStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductStatusController extends Controller
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
            $orderBy = 'order';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = ProductStatus::with(
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
            'code'=>'required|string|unique:product_statuses,code',
            'name'=>'required|string|unique:product_statuses,name',
            'order'=>'required|string|unique:product_statuses,order',
            'is_stock'=>'required',
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id(),'code'=>strtoupper($request->code)]);
        $data = ProductStatus::create($request->all());
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
        $data = ProductStatus::find($id);
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
            'code'=>'required|string',
            'name'=>'required|string',
            'order'=>'required|string',
            'is_stock'=>'required',
        ]);
        $request->merge(['updatedBy'=>Auth::id(),'code'=>strtoupper($request->code)]);
        $data = ProductStatus::find($id);
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
        $ProductStatus = ProductStatus::find($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
    }

    public function getData(Request $request)
    {
        $data = ProductStatus::select('id','code','name')->orderBy('order')->get();
        return response()->json(['data' => $data]);
    }
}
