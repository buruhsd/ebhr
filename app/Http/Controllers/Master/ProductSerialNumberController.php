<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Master\ProductSerialNumber;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductSerialNumberController extends Controller
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
        $data = ProductSerialNumber::with('product',
                    'insertedBy:id,name',
                    'updatedBy:id,name')
                    ->when($search, function ($query) use ($search){
                    $query->whereHas('product', function ($q) use ($search){
                       $q->where('name','LIKE',"{$search}%")
                           ->orwhere('second_name','LIKE',"{$search}%")
                            ->orwhere('register_number','LIKE',"{$search}%");
                    });
                })->when($orderBy, function ($query) use ($orderBy,$sortBy){
                    $query->whereHas('product', function ($q) use ($orderBy,$sortBy){
                        if(is_null($sortBy)){
                            $sortBy = 'asc';
                        }
                        $q->orderBy($orderBy, $sortBy);
                    })->orderBy('id', 'desc');
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
            "product_id" => "required|unique:product_serial_numbers,product_id|distinct|exists:products,id",
            "is_return" => "required",
            "is_serial_number" => "required",
        ]);
        $request->merge([
            'insertedBy' => Auth::id(),
            'updatedBy'=>Auth::id()
        ]);
        ProductSerialNumber::create($request->all());
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ProductSerialNumber::with('product')->find($id);
        return response()->json(['success' => true, 'data'=>$data]);
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
            "product_id" => "required|unique:product_serial_numbers,product_id|distinct|exists:products,id",
            "is_return" => "required",
            "is_serial_number" => "required",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        ProductSerialNumber::find($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
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
            ProductSerialNumber::find($id)->delete();
            return response()->json(['success'=>true,'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['success'=>false,'message' => 'Data tidak boleh dihapus']);
            }
        }
    }
}
