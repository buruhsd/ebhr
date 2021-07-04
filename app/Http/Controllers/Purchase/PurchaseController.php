<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase\PurchaseLetter;
use App\Models\Order\Order;
use App\Http\Resources\Purchase\PurchaseResource;
use App\Http\Resources\Purchase\PurchaseResourceCollection;

class PurchaseController extends Controller
{
    public function index(Request $request){
    	$search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        if(is_null($orderBy)){
            $orderBy = 'id';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
    	$data = PurchaseLetter::with('branch:id,name','transaction_type:id,name','purchase_category:id,name','purchase_necessary:id,name','purchase_urgentity:id,name')
                    ->where('id','LIKE',"%{$search}%")
                    ->orWhere('no_pp', 'LIKE',"{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(20);
        return response()->json(['data' => $data]);
    }

    public function createPurchaseLetter(Request $request){
        $this->validate($request, [
            'tgl_pp' => 'required',
            'no_pp' => 'required',
            'note' => 'required',
            'branch_id' => 'required',
            'transaction_type_id' => 'required',
            'purchase_category_id' => 'required',
            'purchase_necessary_id' => 'required',
            'purchase_urgensity_id' => 'required',
            'insertedBy' => 'required',
            'updatedBy' => 'required',
            'item' => 'required'
        ]);
    	$data = PurchaseLetter::create($request->all());

        foreach($request->item as $item){
            $data->purchase_items()->create($item);
        }

        return response()->json(['data' => $data]);
    }

    public function update(Request $request, PurchaseLetter $purchase){
        $this->validate($request, [
            'tgl_pp' => 'required',
            'no_pp' => 'required',
            'note' => 'required',
            'branch_id' => 'required',
            'transaction_type_id' => 'required',
            'purchase_category_id' => 'required',
            'purchase_necessary_id' => 'required',
            'purchase_urgensity_id' => 'required',
            'insertedBy' => 'required',
            'updatedBy' => 'required',
        ]);

    	$purchase->update($request->except('insertedBy'));
        return response()->json(['data' => $purchase]);
    }

    public function show(PurchaseLetter $purchase){
        return new PurchaseResource($purchase);
    }

    public function delete(PurchaseLetter $purchase){
        $purchase->delete();
        return response()->json(['data' => 'data deleted']);
    }

    public function approval(Request $request, PurchaseLetter $purchase){
    	$status = $request->status;
    	$purchase->update(['status_approval' => $status]);
   		return new PurchaseResource($purchase);
    }

    public function createOrder(Request $request, $id){
        $data = Order::create($request->all());
   		return new OrderResource($data);
    }

    public function closeOrder(Request $request, Order $order){
        $status = $request->status;
        $order->update(['finished_status' => $status]);
   		return new OrderResource($order);
    }

    public function closePurchaseLetter(Request $request, PurchaseLetter $purchase){
        $status = $request->status;
    	$purchase->update(['status_approval' => $status]);
   		return new PurchaseResource($purchase);
    }
}
