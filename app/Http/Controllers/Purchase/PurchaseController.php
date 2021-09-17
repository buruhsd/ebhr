<?php

namespace App\Http\Controllers\Purchase;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase\PurchaseLetter;
use App\Models\Order\Order;
use App\Models\Purchase\PurchaseLetterItem;
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
    	$data = PurchaseLetter::with('branch:id,name',
                    'warehouse:id,name',
                    'transaction_type:id,name',
                    'purchase_category:id,name',
                    'purchase_necessary:id,name',
                    'purchase_urgentity:id,name',
                    'purchase_items:id,product_id,purchase_letter_id,qty,unit',
                    'purchase_items.products:id,name,product_code,second_name,register_number')
                    ->where('id','LIKE',"{$search}%")
                    ->orWhere('no_pp', 'LIKE',"{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(20);
        return response()->json(['data' => $data]);
    }

    public function createPurchaseLetter(Request $request){
        $this->validate($request, [
            'tgl_pp' => 'required',
            'note' => 'required',
            'branch_id' => 'required',
            'warehouse_id' => 'required',
            'transaction_type_id' => 'required',
            'purchase_category_id' => 'required',
            'purchase_necessary_id' => 'required',
            'purchase_urgensity_id' => 'required',
            'item' => 'required',
            'item.*.product_id' => 'required|distinct',
            'item.*.qty' => 'required',
            'item.*.unit' => 'required',
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
    	$data = PurchaseLetter::create($request->all());

        foreach($request->item as $item){
            $item['insertedBy'] = Auth::id();
            $item['updatedBy'] = Auth::id();
            $data->purchase_items()->create($item);
        }

        return response()->json(['data' => $data]);
    }

    public function update(Request $request, PurchaseLetter $purchase){
        $this->validate($request, [
            'tgl_pp' => 'required',
            'note' => 'required',
            'transaction_type_id' => 'required',
            'purchase_category_id' => 'required',
            'purchase_necessary_id' => 'required',
            'purchase_urgensity_id' => 'required',
            'item' => 'required',
            'item.*.product_id' => 'required|distinct',
            'item.*.qty' => 'required',
            'item.*.unit' => 'required',
        ]);

        $request->merge(['updatedBy'=>Auth::id()]);
    	$purchase->update($request->except(['branch_id','warehouse_id','no_pp']));
        foreach($request->item as $item){
            $dataUpdate = $purchase->purchase_items()->where('product_id',$item['product_id'])->first();
            if($dataUpdate){
                $item['updatedBy'] = Auth::id();
                $dataUpdate->update($item);
            }else{
                $item['insertedBy'] = Auth::id();
                $item['updatedBy'] = Auth::id();
                $purchase->purchase_items()->create($item);
            }
        }
        return response()->json(['data' => $purchase]);
    }

    public function show(PurchaseLetter $purchase){
        return new PurchaseResource($purchase);
    }

    public function delete(PurchaseLetter $purchase){
        $purchase->purchase_items()->delete();
        $purchase->delete();
        return response()->json(['data' => 'data deleted']);
    }

    public function getData(Request $request)
    {
        $search = $request->q;
        $branch = $request->b;
    	$data = PurchaseLetter::with('purchase_items:id,product_id,purchase_letter_id,qty,unit',
                    'purchase_items.products:id,name,product_code,second_name,register_number','purchase_items.products.units:product_id,unit_id,value','purchase_items.products.units.unit:id,name')
                ->where('is_order',0)
                ->where('branch_id', $branch)
                ->where(function ($query) use ($search){
                    $query->where('no_pp', 'LIKE',"%{$search}%");
                })
                ->get();
        return response()->json(['data' => $data]);
    }

    public function getItemPurchase(Request $request)
    {
        $search = $request->q;
    	$data = PurchaseLetterItem::select('id','purchase_letter_id','product_id','qty','unit')
                ->with('purchase:id,no_pp','products:id,register_number,second_name,unit_id','products.unit:id,name','products.units:id,product_id,unit_id,value')
                ->where('status',0)
                ->wherehas('purchase', function ($query) use ($search){
                    $query->where('purchase_letters.branch_id', $search);
                })
                ->get();
        return response()->json(['data' => $data]);
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

    public function getNumberPP(Request $request, $id)
    {
        $number = PurchaseLetter::numberPP($id);
        return response()->json(['data' => $number]);
    }
}
