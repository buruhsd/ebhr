<?php

namespace App\Http\Controllers\Purchase;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Models\Kurs;
use App\Models\Supplier;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Purchase\PurchaseLetter;
use App\Models\Purchase\PurchaseLetterItem;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    // status 0 = new, 1 = approved, 2 = released, 3 = closed

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
                    'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
                    'supplier.currency:id,code,name',
                    'supplier.partner:id,code,name',
                    'kurs_type:id,name',
                    'order_item',
                    'order_item.purchase_item:id,product_id,qty,unit',
                    'order_item.product:id,register_number,name,second_name',
                    'order_item.product.units:id,name,value',
                    'order_item.unit:id,name')
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
            'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
            'supplier.currency:id,code,name',
            'supplier.partner:id,code,name',
            'kurs_type:id,name',
            'order_item',
            'order_item.purchase_item:id,product_id,qty,unit',
            'order_item.product:id,register_number,name,second_name',
            'order_item.product.units:id,name,value',
            'order_item.unit:id,name')->find($id);
        if($data){
            return response()->json(['success' => true, 'data' => $data]);
        }
        return response()->json(['success' => false, 'message' => 'Data tidak ada']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'supplier_id' => '|exists:suppliers,id',
            'date_op' => 'required|date',
            'date_estimate' => 'required|date',
            'ppn' => 'required|numeric',
            'kurs_type_id' => 'required|exists:kurs_types,id',
            'noted' => 'required',
            'item' => 'required',
            'item.*.purchase_letter_id' => 'required|exists:purchase_letters,id',
            'item.*.purchase_letter_item_id' => 'required|distinct|exists:purchase_letter_items,id',
            'item.*.unit_id' => 'required|numeric|exists:units,id',
            'item.*.qty' => 'required||min:1',
            'item.*.price' => 'required',
            'item.*.discount' => 'required|numeric',
        ]);
        $total = 0;
        $max_price_unit = 0;
        $max_price_item = 0;
        foreach($request->item as $value){
            $purchase_item = PurchaseLetterItem::find($value['purchase_letter_item_id']);
            $totalQtyKonversi = DB::table('purchase_order_items')
                    ->join('product_units', 'product_units.unit_id', '=', 'purchase_order_items.unit_id')
                    ->where('purchase_order_items.purchase_letter_item_id', $purchase_item->id)
                    ->where('product_units.product_id', $purchase_item->product_id)
                    ->sum(DB::raw('qty * product_units.value'));
            $konversiQty = $purchase_item->products->units()->where('unit_id',$value['unit_id'])->first()->value;
            $nilai = $value['qty'] * $konversiQty;
            $totalQty = $totalQtyKonversi + $nilai;
            $price = str_replace('.','',$value['price']);
            if($price > $max_price_unit){
                $max_price_unit = $price;
            }
            $total_item = $value['qty'] * $price;
            if($total_item > $max_price_item){
                $max_price_item = $total_item;
            }
            $total += $total_item;
            if($totalQty > $purchase_item->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah OP tidak boleh melebihi dari jumlah PP']);
            }
        }

        $currency_id = Supplier::find($request->supplier_id)->currency_id;
        $kurs = Kurs::where(['currency_id'=>$currency_id,'kurs_type_id'=>$request->kurs_type_id])
                ->whereDate('date','<=',now())
                ->orderBy('date','desc')
                ->first()->value;
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id(),'kurs'=>$kurs,'total'=>$total,'max_price_unit'=>$max_price_unit,'max_price_item'=>$max_price_item]);
    	$data = PurchaseOrder::create($request->all());
        foreach($request->item as $value){
            $purchase_item = PurchaseLetterItem::find($value['purchase_letter_item_id']);
            $totalQtyKonversi = DB::table('purchase_order_items')
                    ->join('product_units', 'product_units.unit_id', '=', 'purchase_order_items.unit_id')
                    ->where('purchase_order_items.purchase_letter_item_id', $purchase_item->id)
                    ->where('product_units.product_id', $purchase_item->product_id)
                    ->sum(DB::raw('qty * product_units.value'));
            $konversiQty = $purchase_item->products->units()->where('unit_id',$value['unit_id'])->first()->value;
            $nilai = $value['qty'] * $konversiQty;
            $totalQty = $totalQtyKonversi + $nilai;

            $price = str_replace('.','',$value['price']);
            $net = $price - ($price * ($value['discount']/100));
            $value['product_id'] = $purchase_item->product_id;
            $value['price'] = $price;
            $value['net'] = $net;
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $data->order_item()->create($value);
            if($totalQty == $purchase_item->qty){
                $purchase_item->status = 1;
                $purchase_item->save();
            }
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'supplier_id' => '|exists:suppliers,id',
            'date_op' => 'required|date',
            'date_estimate' => 'required|date',
            'kurs_type_id' => 'required|exists:kurs_types,id',
            'noted' => 'required',
            'item' => 'required',
            'item.*.purchase_order_item_id' => 'required',
            'item.*.purchase_letter_id' => 'required|exists:purchase_letters,id',
            'item.*.purchase_letter_item_id' => 'required|distinct|exists:purchase_letter_items,id',
            'item.*.unit_id' => 'required|numeric|exists:units,id',
            'item.*.qty' => 'required||min:1',
            'item.*.price' => 'required',
            'item.*.discount' => 'required|numeric',
        ]);

        $order = PurchaseOrder::find($id);
        if(is_null($order)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }
        $total = 0;
        $max_price_unit = 0;
        $max_price_item = 0;
        foreach($request->item as $value){
            $purchase_item = PurchaseLetterItem::find($value['purchase_letter_item_id']);
            $totalQtyKonversi = DB::table('purchase_order_items')
                    ->join('product_units', 'product_units.unit_id', '=', 'purchase_order_items.unit_id')
                    ->where('purchase_order_items.id','!=',$value['purchase_order_item_id'])
                    ->where('purchase_order_items.purchase_letter_item_id', $purchase_item->id)
                    ->where('product_units.product_id', $purchase_item->product_id)
                    ->sum(DB::raw('qty * product_units.value'));
            $konversiQty = $purchase_item->products->units()->where('unit_id',$value['unit_id'])->first()->value;
            $nilai = $value['qty'] * $konversiQty;
            $totalQty = $totalQtyKonversi + $nilai;
            $price = str_replace('.','',$value['price']);
            if($price > $max_price_unit){
                $max_price_unit = $price;
            }
            $total_item = $value['qty'] * $price;
            if($total_item > $max_price_item){
                $max_price_item = $total_item;
            }
            $total += $total_item;
            if($totalQty > $purchase_item->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah OP tidak boleh melebihi dari jumlah PP']);
            }
        }
        $currency_id = Supplier::find($request->supplier_id)->currency_id;
        $kurs = Kurs::where(['currency_id'=>$currency_id,'kurs_type_id'=>$request->kurs_type_id])
                ->whereDate('date','<=',now())
                ->orderBy('date','desc')
                ->first()->value;
        $request->merge(['updatedBy'=>Auth::id(),'kurs'=>$kurs,'total'=>$total,'max_price_unit'=>$max_price_unit,'max_price_item'=>$max_price_item]);
    	$order->update($request->all());
        foreach($request->item as $value){
            $purchase_item = PurchaseLetterItem::find($value['purchase_letter_item_id']);
            $update = $order->order_item()->where('id', $value['purchase_order_item_id'])->first();
            if($update){
                $totalQtyKonversi = DB::table('purchase_order_items')
                    ->join('product_units', 'product_units.unit_id', '=', 'purchase_order_items.unit_id')
                    ->where('purchase_order_items.id','!=',$value['purchase_order_item_id'])
                    ->where('purchase_order_items.purchase_letter_item_id', $purchase_item->id)
                    ->where('product_units.product_id', $purchase_item->product_id)
                    ->sum(DB::raw('qty * product_units.value'));
                $konversiQty = $purchase_item->products->units()->where('unit_id',$value['unit_id'])->first()->value;
                $nilai = $value['qty'] * $konversiQty;
                $totalQty = $totalQtyKonversi + $nilai;
                $price = str_replace('.','',$value['price']);
                $net = $price - ($price * ($value['discount']/100));
                $value['product_id'] = $purchase_item->product_id;
                $value['price'] = $price;
                $value['net'] = $net;
                $item['updatedBy'] = Auth::id();
                $update->update($value);
                $status = 0;
                if($totalQty == $purchase_item->qty){
                    $status = 1;
                }
                $purchase_item->status = $status;
                $purchase_item->save();
            }else{
                $totalQtyKonversi = DB::table('purchase_order_items')
                        ->join('product_units', 'product_units.unit_id', '=', 'purchase_order_items.unit_id')
                        ->where('purchase_order_items.purchase_letter_item_id', $purchase_item->id)
                        ->where('product_units.product_id', $purchase_item->product_id)
                        ->sum(DB::raw('qty * product_units.value'));
                $konversiQty = $purchase_item->products->units()->where('unit_id',$value['unit_id'])->first()->value;
                $nilai = $value['qty'] * $konversiQty;
                $totalQty = $totalQtyKonversi + $nilai;

                $price = str_replace('.','',$value['price']);
                $net = $price - ($price * ($value['discount']/100));
                $value['product_id'] = $purchase_item->product_id;
                $value['price'] = $price;
                $value['net'] = $net;
                $value['insertedBy'] = Auth::id();
                $value['updatedBy'] = Auth::id();
                $data->order_item()->create($value);
                if($totalQty == $purchase_item->qty){
                    $purchase_item->status = 1;
                    $purchase_item->save();
                }
            }
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function FunctionName(Type $var = null)
    {


        $item = PurchaseLetterItem::with('products.units')->find($request->purchase_letter_item_id);
        $totalQtyKonversi = DB::table('purchase_orders')
                ->join('product_units', 'product_units.unit_id', '=', 'purchase_orders.unit_id')
                ->where('product_units.product_id', '<=', $item->product_id)
                ->where('purchase_orders.id', '!=', $id)
                ->sum(DB::raw('qty * product_units.value'));
        $konversiQty = $item->products->units->where('unit_id',$request->unit_id)->first()->value;
        $nilai = $request->qty * $konversiQty;
        $totalQty = $totalQtyKonversi + $nilai;
        if($totalQty > $item->qty){
            return response()->json(['success' => false, 'message' => 'Jumlah OP tidak boleh melebihi dari jumlah PP']);
        }
        $price = str_replace('.','',$request->price);
        $net = $price - ($price * ($request->discount/100));
        $request->merge(['updatedBy'=>Auth::id(),'price'=>$price,'net'=>$net]);
        $order = PurchaseOrder::find($id);
    	$order->update($request->all());
        $is_order = false;
        if($totalQty == $item->qty){
            $is_order = true;
        }
        PurchaseLetter::find($request->purchase_letter_id)->update(['is_order' => $is_order]);
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
                    'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
                    'supplier.currency:id,code,name',
                    'supplier.partner:id,code,name',
                    'order_item',
                    'purchase_letter')
                ->when($search, function ($query) use ($search){
                    $query->where('no_op', 'LIKE',"%{$search}%");
                })
                ->limit(10)
                ->get();
        return response()->json(['data' => $data]);
    }

    public function getNumberOP(Request $request, $id)
    {
        $number = PurchaseOrder::numberOP($id);
        return response()->json(['data' => $number]);
    }

    public function description(Request $request)
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
                    'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
                    'supplier.currency:id,code,name',
                    'supplier.partner:id,code,name',
                    'kurs_type:id,name',
                    'order_item',
                    'order_item.purchase_item:id,product_id,qty,unit',
                    'order_item.product:id,register_number,name,second_name',
                    'order_item.product.units:id,name,value',
                    'order_item.unit:id,name',
                    'purchase_letter','description:id,purchase_order_id,noted')
                ->when($search, function ($query) use ($search){
                    $query->where('no_op','LIKE',"{$search}%");
                })
                ->orderBy($orderBy, $sortBy)
                ->paginate(20);
        return response()->json($data);
    }

    public function approve(Request $request,$id)
    {
        $order = PurchaseOrder::find($id);
        $order->status = 1;
        $order->approved_by = Auth::id();
        $order->approved_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil diapprove']);
    }

    public function release(Request $request,$id)
    {
        $order = PurchaseOrder::find($id);
        $order->status = 2;
        $order->released_by = Auth::id();
        $order->released_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil direlease']);
    }

    public function close(Request $request,$id)
    {
        $order = PurchaseOrder::find($id);
        $order->status = 3;
        $order->closed_by = Auth::id();
        $order->closed_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil diclose']);
    }
}
