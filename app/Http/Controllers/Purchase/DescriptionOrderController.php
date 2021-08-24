<?php

namespace App\Http\Controllers\Purchase;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Purchase\PurchaseDescription;

class DescriptionOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        if(is_null($orderBy)){
            $orderBy = 'id';
        }
        if(is_null($sortBy)){
            $sortBy = 'desc';
        }
    	$data = PurchaseDescription::with('order')
                ->where('id','LIKE',"%{$search}%")
                ->orWhere('item_name', 'LIKE',"{$search}%")
                ->orderBy($orderBy, $sortBy)
                ->paginate(20);
        return response()->json($data);
    }

    public function show($id)
    {
        $data = PurchaseDescription::with('order')->find($id);
        if($data){
            return response()->json(['success' => true, 'data' => $data]);
        }
        return response()->json(['success' => false, 'message' => 'Data tidak ada']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'item_name' => 'required',
            'brand' => 'required',
            'type' => 'required',
            'noted' => 'required',
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id(),'status'=>1]);
    	PurchaseDescription::create($request->all());
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'item_name' => 'required',
            'brand' => 'required',
            'type' => 'required',
            'noted' => 'required',
        ]);

        $request->merge(['updatedBy'=>Auth::id()]);
        $order = PurchaseDescription::find($id);
    	$order->update($request->all());
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        $data = PurchaseDescription::find($id)->delete();
        return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
    }
}
