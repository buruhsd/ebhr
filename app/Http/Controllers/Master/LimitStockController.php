<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\LimitStock;
use App\Models\Master\Products;
use App\Models\Master\Warehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LimitStockController extends Controller
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
            $orderBy = 'id';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = LimitStock::with(
            'product:id,register_number,name,second_name',
            'warehouse:id,code,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->when($search, function ($query) use ($search){
                $query->whereHas('product', function ($product) use ($search){
                    $product->where('products.register_number',$search)
                        ->orWhere('products.name',$search)
                        ->orWhere('products.second_name',$search);
                })
                ->orWhereHas('warehouse', function ($level) use ($search){
                    $level->where('warehouses.name',$search);
                });
            })
            ->when($orderBy, function ($query) use ($orderBy,$sortBy){
                if($orderBy == 'register_number' || $orderBy == 'name'){
                    $query->orderBy(Products::select($orderBy)
                        ->whereColumn('products.id', 'limit_stocks.product_id')
                    ,$sortBy);
                }elseif($orderBy == 'warehouse'){
                    $query->orderBy(Warehouse::select('name')
                        ->whereColumn('warehouses.id', 'limit_stocks.warehouse_id')
                    ,$sortBy);
                }else{
                    $query->orderBy($orderBy, $sortBy);
                }
            })
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
            'product_id' => 'required|exists:products,id|unique:limit_stocks,product_id,'.$request->product_id.',id,warehouse_id,'.$request->warehouse_id,
            'warehouse_id' => 'required|exists:warehouses,id|unique:limit_stocks,warehouse_id,'.$request->warehouse_id.',id,product_id,'.$request->product_id,
            "min" => "required|numeric",
            "max" => "required|numeric",
            "expired_at" => "required|date",
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = LimitStock::create($request->all());
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
        $data = LimitStock::with(
            'product:id,register_number,name,second_name',
            'warehouse:id,code,name',
            'insertedBy:id,name',
            'updatedBy:id,name')->find($id);
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
            'product_id' => 'required|exists:products,id|unique:limit_stocks,product_id,'.$id.',id,warehouse_id,'.$request->warehouse_id,
            'warehouse_id' => 'required|exists:warehouses,id|unique:limit_stocks,warehouse_id,'.$id.',id,product_id,'.$request->product_id,
            "min" => "required|numeric",
            "max" => "required|numeric",
            "expired_at" => "required|date",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = LimitStock::find($id);
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
            LimitStock::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }
}