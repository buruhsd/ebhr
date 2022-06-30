<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Master\ProductUnit;
use App\Models\Master\Products as Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-product', ['except' => ['index']]);
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
        $approve = $request->approve;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        if(is_null($orderBy)){
            $orderBy = 'name';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = Product::with('unit:id,code,name',
            'units:id,product_id,unit_id,name,type,value',
            'units.unit:id,code,name',
            'category:id,code,name,parent_id',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->when($search, function($query) use ($search,$orderBy){
                $query->where($orderBy,'LIKE',"%{$search}%");
            })
            ->when($approve, function($query) use ($approve){
                $query->where('is_approve',$approve);
            })
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
            "product_code" => "required|unique:products,product_code",
            'name' => "required",
            'second_name' => "required",
            'spesification' => "required",
            'product_number' => "required",
            'type' => "required",
            'brand' => "required",
            'vendor' => "required",
            'barcode' => "required",
            'unit_id' => "required",
            'status' => "required",
            'category_id' => "required",
            'unit' => 'required',
            'unit.*.unit_id' => 'required',
            'unit.*.value' => 'required',
        ]);

        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Product::create($request->all());
        foreach($request->unit as $key => $item){
            $type = 'Intern';
            if($key == 0){
                $type = 'Extern';
                $item['value'] = 1;
            }
            $item['name'] = 'Unit '.++$key;
            $item['type'] = $type;
            $item['insertedBy'] = Auth::id();
            $item['updatedBy'] = Auth::id();
            $data->units()->create($item);
        }
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
        $data = Product::find($id);
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
            "product_code" => "required|unique:products,product_code,".$id,
            'name' => "required",
            'second_name' => "required",
            'spesification' => "required",
            'product_number' => "required",
            'type' => "required",
            'brand' => "required",
            'vendor' => "required",
            'barcode' => "required",
            'unit_id' => "required",
            'status' => "required",
            'category_id' => "required",
            'unit' => 'required',
            'unit.*.unit_id' => 'required',
            'unit.*.value' => 'required',
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Product::find($id);
        $data->update($request->all());
        $data->units()->delete();
        foreach($request->unit as $key => $item){
            $type = 'Intern';
            if($key == 0){
                $type = 'Extern';
                $item['value'] = 1;
            }
            $item['name'] = 'Unit '.++$key;
            $item['type'] = $type;
            $item['insertedBy'] = Auth::id();
            $item['updatedBy'] = Auth::id();
            $data->units()->create($item);
        }
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
            ProductUnit::where('product_id',$id)->delete();
            Product::find($id)->delete();
            return response()->json(['success'=>true,'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['success'=>false,'message' => 'Data tidak boleh dihapus']);
            }
        }
    }

    public function approve(Request $request,$id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(['success'=>false,'message' => 'Data tidak ada']);
        }
        $product->is_approve = 1;
        $product->save();
        return response()->json(['success'=>true,'message' => 'Data berhasil diapprove']);
    }
}
