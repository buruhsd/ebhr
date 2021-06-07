<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Models\Purchase\PurchaseLetter;
use Models\Order\Order;
use App\Http\Resources\Purchase\PurchaseResource;
use App\Http\Resources\Purchase\PurchaseResourceCollection;

class PurchaseController extends Controller
{
    public function index(){
    	$search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');

    	$data = PurchaseLetter::where('id','LIKE',"%{$search}%")
                    ->orWhere('name', 'LIKE',"%{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(10);

        return new UserResourceCollection($data);
    }

    public function createPurchaseLetter(){
    	$data = PurchaseLetter::create($request->all());
        return new PurchaseResource($data);
    }

    public function update(Request $request, PurchaseLetter $purchaseLetter){
    	$data = $purchaseLetter->update($request->all());

        return new PurchaseResource($data);
    }

    public function show(PurchaseLetter $purchaseLetter){
        return new PurchaseResource($data);
    }

    public function delete(PurchaseLetter $purchaseLetter){
        $purchaseLetter->delete();
        return response()->json(['data' => 'data deleted']);
    }

    public function approval(Request $request, PurchaseLetter $purchaseLetter){
    	$status = $request->status;
    	$data = $PurchaseLetter->update(['status_approval' => $status]);
   		return new PurchaseResource($data);
    }

    public function createOrder(Request $request, $id){
        $data = Order::create($request->all());
   		return new OrderResource($data);
    }

    public function closeOrder(Request $request, Order $order){
        $status = $request->status;
        $data = $order->update(['finished_status' => $status]);
   		return new OrderResource($data);
    }

    public function closePurchaseLetter(Request $request, PurchaseLetter $purchaseLetter){
        $status = $request->status;
    	$data = $PurchaseLetter->update(['status_approval' => $status]);
   		return new PurchaseResource($data);
    }
}
