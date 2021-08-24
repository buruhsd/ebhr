<?php

namespace App\Http\Controllers\Purchase;

use Auth;
use Illuminate\Http\Request;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseLetter;
use App\Models\Purchase\PurchaseLetterItem;
use App\Http\Controllers\Controller;

class OrderController extends Controller
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
    	$data = PurchaseOrder::with('branch:id,name',
                    'transaction_type:id,name',
                    'supplier',
                    'item.products',
                    'purchase_letter')
                ->where('id','LIKE',"{$search}%")
                ->orWhere('no_op', 'LIKE',"{$search}%")
                ->orderBy($orderBy, $sortBy)
                ->paginate(20);
        return response()->json($data);
    }

    public function show($id)
    {
        $data = PurchaseOrder::with('branch:id,name',
            'transaction_type:id,name',
            'supplier',
            'item.products',
            'purchase_letter')->find($id);
        if($data){
            return response()->json(['success' => true, 'data' => $data]);
        }
        return response()->json(['success' => false, 'message' => 'Data tidak ada']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'purchase_letter_id' => 'required|exists:purchase_letters,id',
            'purchase_letter_item_id' => 'required|exists:purchase_letter_items,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'supplier_id' => '|exists:suppliers,id',
            'date_op' => 'required|date',
            'date_estimate' => 'required|date',
            'ppn' => 'required|numeric',
            'qty' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'discount' => 'required|numeric',
            'noted' => 'required',
        ]);

        $checkOrderQty = PurchaseOrder::where([
            'branch_id'=>$request->branch_id,
            'purchase_letter_id'=>$request->purchase_letter_id,
            'purchase_letter_item_id'=>$request->purchase_letter_item_id])
            ->sum('qty');
        $totalQty = $request->qty + $checkOrderQty;
        $item = PurchaseLetterItem::find($request->purchase_letter_item_id);
        if($totalQty > $item->qty){
            return response()->json(['success' => false, 'message' => 'Jumlah tidak boleh melebihi jumlah barang dari permintaan pembelian']);
        }
        $net = $request->price - ($request->price * ($request->discount/100));
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id(),'net'=>$net]);
    	PurchaseOrder::create($request->all());
        if($totalQty == $item->qty){
            PurchaseLetter::find($request->purchase_letter_id)->update(['is_order' => 1]);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'purchase_letter_id' => 'required|exists:purchase_letters,id',
            'purchase_letter_item_id' => 'required|exists:purchase_letter_items,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'supplier_id' => '|exists:suppliers,id',
            'date_op' => 'required|date',
            'date_estimate' => 'required|date',
            'ppn' => 'required|numeric',
            'qty' => 'required|numeric',
            'price' => 'required|numeric',
            'discount' => 'required|numeric',
            'noted' => 'required',
        ]);

        $data = PurchaseOrder::find($id);
        if(is_null($data)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        $checkOrderQty = PurchaseOrder::where([
            'branch_id'=>$request->branch_id,
            'purchase_letter_id'=>$request->purchase_letter_id,
            'purchase_letter_item_id'=>$request->purchase_letter_item_id])
            ->sum('qty');
        $totalQty = $request->qty + $checkOrderQty;
        $item = PurchaseLetterItem::find($request->purchase_letter_item_id);
        if($totalQty > $item->qty){
            return response()->json(['success' => false, 'message' => 'Jumlah tidak boleh melebihi jumlah barang dari permintaan pembelian']);
        }

        $net = $request->price - ($request->price * ($request->discount/100));
        $request->merge(['updatedBy'=>Auth::id(),'net'=>$net]);
        $order = PurchaseOrder::find($id);
    	$order->update($request->all());
        $is_order = false;
        if($totalQty == $item->qty){
            $is_order = true;
        }
        PurchaseLetter::find($request->purchase_letter_id)->update(['is_order' => $is_order]);
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        try {
            $data = PurchaseOrder::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $search = $request->q;
    	$data = PurchaseOrder::with('branch:id,name',
                    'transaction_type:id,name',
                    'supplier',
                    'item.products',
                    'purchase_letter')
                ->when($search, function ($query) use ($search){
                    $query->where('no_op', 'LIKE',"%{$search}%");
                })
                ->get();
        return response()->json(['data' => $data]);
    }
}
