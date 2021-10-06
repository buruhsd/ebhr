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
    // status 0 = new, 1 = approved, 2 = Reject approved, 3 = released, 4 = Reject released, 5 = closed, 6 = On Process, 7 = Done
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$branch = $request->branch;
    	$search = $request->search;
    	$status = $request->status;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        if(is_null($orderBy)){
            $orderBy = 'id';
        }
        if(is_null($sortBy)){
            $sortBy = 'desc';
        }


        if(is_null($from_date) || is_null($to_date)){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime($to_date. '-10 months'));
        }

    	$data = PurchaseOrder::with('branch:id,name',
                    'transaction_type:id,name',
                    'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
                    'supplier.currency:id,code,name',
                    'supplier.partner:id,code,name',
                    'kurs_type:id,name',
                    'currency:id,code,name',
                    'approved_by:id,name',
                    'released_by:id,name',
                    'closed_by:id,name',
                    'order_item',
                    'order_item.purchase:id,no_pp',
                    'order_item.purchase_item:id,product_id,qty,rest_qty,unit',
                    'order_item.product:id,register_number,name,second_name',
                    'order_item.product.units:id,name,value',
                    'order_item.unit:id,name')
                ->when($status, function ($query) use ($status){
                    if($status == 'new'){
                        $statusIn = [0];
                    }elseif($status == 'app'){
                        $statusIn = [1];
                    }elseif($status == 'reject_app'){
                        $statusIn = [2];
                    }elseif($status == 'release'){
                        $statusIn = [3];
                    }elseif($status == 'reject_release'){
                        $statusIn = [4];
                    }elseif($status == 'close'){
                        $statusIn = [5];
                    }elseif($status == 'on_proses'){
                        $statusIn = [6];
                    }elseif($status == 'done'){
                        $statusIn = [7];
                    }elseif($status == 'all'){
                        $statusIn = [0,1,2,3,4,6];
                    }
                    $query->whereIn('status',$statusIn);
                })
                ->when($search, function ($query) use ($search){
                    $query->where('no_op','LIKE',"{$search}%");
                })
                ->when($branch, function ($query) use ($branch){
                    $query->whereHas('branch',function ($q) use ($branch){
                        $q->where('branches.id',$branch);
                    });
                })
                ->whereDate('date_op','>=',$from_date)
                ->whereDate('date_op','<=',$to_date)
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
            'order_item.purchase_item:id,product_id,qty,rest_qty,unit',
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
            'kurs_type_id' => 'nullable|exists:kurs_types,id',
            'noted' => 'required',
            'item' => 'required',
            'item.*.purchase_letter_id' => 'required|exists:purchase_letters,id',
            'item.*.purchase_letter_item_id' => 'required|distinct|exists:purchase_letter_items,id',
            'item.*.unit_id' => 'required|numeric|exists:units,id',
            'item.*.qty' => 'required||min:1',
            'item.*.price' => 'nullable',
            'item.*.price_hc' => 'required',
            'item.*.discount' => 'required|numeric',
        ]);
        $total = 0;
        $max_price_unit = 0;
        $max_price_item = 0;
        $kurs = 0;
        $term_of_payment = 0;
        $currency_id = NULL;
        $supplier = Supplier::find($request->supplier_id);
        if($supplier && $request->kurs_type_id){
            $currency_id = $supplier->currency_id;
            $term_of_payment = $supplier->term_of_payment;
            $kurs = Kurs::where(['currency_id'=>$currency_id,'kurs_type_id'=>$request->kurs_type_id])
                    ->whereDate('date','<=',now())
                    ->orderBy('date','desc')
                    ->first()->value;
        }
        foreach($request->item as $value){
            $purchase_item = PurchaseLetterItem::find($value['purchase_letter_item_id']);
            $totalQtyKonversi = DB::table('purchase_order_items')
                    ->join('product_units', 'product_units.unit_id', '=', 'purchase_order_items.unit_id')
                    ->where('purchase_order_items.purchase_letter_item_id', $purchase_item->id)
                    ->where('product_units.product_id', $purchase_item->product_id)
                    ->sum(DB::raw('qty * product_units.value'));
            $konversiQty = $purchase_item->products->units()->where('unit_id',$value['unit_id'])->first()->value;
            $nilai = $value['qty'] * $konversiQty;
            if($nilai > $purchase_item->rest_qty){
                return response()->json(['success' => false, 'message' => 'Jumlah OP tidak boleh melebihi dari jumlah PP']);
            }
            $totalQty = $totalQtyKonversi + $nilai;
            $discount = $value['discount'];
            $price = $value['price'] * $kurs;
            $price = $price - ($price * ($value['discount']/100));
            // $price = str_replace(',','.',$price);
            if($kurs == 0){
                $price = $value['price_hc'];
                $price = $price - ($price * ($value['discount']/100));
                // $price = str_replace(',','.',$price);
            }

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
        $dpp = round($total,0);
        $dpp = intval(substr_replace($dpp,"000",-3));
        $ppn_hc = $dpp * ($request->ppn/100);
        $total_op = $total + $ppn_hc;
        $request->merge([
            'term_of_payment'=>$term_of_payment,
            'kurs'=> $kurs,
            'currency_id'=> $currency_id,
            'ppn_hc'=> $ppn_hc,
            'dpp'=> $dpp,
            'total'=> round($total_op,2),
            'max_price_unit'=> round($max_price_unit,2),
            'max_price_item'=> round($max_price_item,2),
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);
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

            $price = $value['price'];
            // $price = str_replace(',','.',$price);
            $price_hc = $value['price_hc'];
            // $price_hc = str_replace(',','.',$price_hc);
            if($kurs > 0){
                $price_hc = $price * $kurs;
            }else{
                $price = 0;
            }
            $net = $price - ($price * ($value['discount']/100));
            $net_hc = $price_hc - ($price_hc * ($value['discount']/100));
            $value['product_id'] = $purchase_item->product_id;
            $value['price'] = $price;
            $value['price_hc'] = $price_hc;
            $value['net'] = $net;
            $value['net_hc'] = $net_hc;
            $value['rest_qty'] = $value['qty'];
            $value['unit_conversion'] = $konversiQty;
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $data->order_item()->create($value);
            if($totalQty == $purchase_item->qty){
                $purchase_item->status = 1;
            }
            $purchase_item->rest_qty = $purchase_item->rest_qty - $nilai;
            $purchase_item->save();
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
            'kurs_type_id' => 'nullable|exists:kurs_types,id',
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
        $kurs = 0;
        $term_of_payment = 0;
        $currency_id = NULL;
        $supplier = Supplier::find($request->supplier_id);
        if($supplier && $request->kurs_type_id){
            $currency_id = $supplier->currency_id;
            $term_of_payment = $supplier->term_of_payment;
            $kurs = Kurs::where(['currency_id'=>$currency_id,'kurs_type_id'=>$request->kurs_type_id])
                    ->whereDate('date','<=',now())
                    ->orderBy('date','desc')
                    ->first()->value;
        }
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
            // if($nilai > $purchase_item->rest_qty){
            //     return response()->json(['success' => false, 'message' => 'Jumlah OP tidak boleh melebihi dari jumlah PP']);
            // }
            $totalQty = $totalQtyKonversi + $nilai;
            $discount = $value['discount'];
            $price = $value['price'] * $kurs;
            $price = $price - ($price * ($value['discount']/100));
            // $price = str_replace(',','.',$price);
            if($kurs == 0){
                $price = $value['price_hc'];
                $price = $price - ($price * ($value['discount']/100));
                // $price = str_replace(',','.',$price);
            }

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
        $dpp = round($total,0);
        $dpp = intval(substr_replace($dpp,"000",-3));
        $ppn_hc = $dpp * ($request->ppn/100);
        $total_op = $total + $ppn_hc;
        $request->merge([
            'term_of_payment'=>$term_of_payment,
            'kurs'=> $kurs,
            'currency_id'=> $currency_id,
            'ppn_hc'=> $ppn_hc,
            'dpp'=> $dpp,
            'total'=> round($total_op,2),
            'max_price_unit'=> round($max_price_unit,2),
            'max_price_item'=> round($max_price_item,2),
            'updatedBy'=> Auth::id()
        ]);

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

                $price = $value['price'];
                // $price = str_replace(',','.',$price);
                $price_hc = $value['price_hc'];
                // $price_hc = str_replace(',','.',$price_hc);
                if($kurs > 0){
                    $price_hc = $price * $kurs;
                }else{
                    $price = 0;
                }
                $net = $price - ($price * ($value['discount']/100));
                $net_hc = $price_hc - ($price_hc * ($value['discount']/100));
                $value['product_id'] = $purchase_item->product_id;
                $value['price'] = $price;
                $value['price_hc'] = $price_hc;
                $value['net'] = $net;
                $value['net_hc'] = $net_hc;
                $value['rest_qty'] = $value['qty'];
                $value['unit_conversion'] = $konversiQty;
                $value['updatedBy'] = Auth::id();
                $update->update($value);
                $status = 0;
                if($totalQty == $purchase_item->qty){
                    $status = 1;
                }
                $rest = PurchaseOrderItem::where(['purchase_letter_id'=> $value['purchase_letter_id'],'purchase_letter_item_id'=> $value['purchase_letter_item_id']])->sum(DB::raw('unit_conversion * qty'));
                $purchase_item->rest_qty = $purchase_item->qty - $rest;
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
                $price = str_replace(',','.',$price);
                $price_hc = str_replace('.','',$value['price_hc']);
                $price_hc = str_replace(',','.',$price_hc);
                if($kurs > 0){
                    $price_hc = $price * $kurs;
                }else{
                    $price = 0;
                }
                $net = $price - ($price * ($value['discount']/100));
                $net_hc = $price_hc - ($price_hc * ($value['discount']/100));
                $value['product_id'] = $purchase_item->product_id;
                $value['price'] = $price;
                $value['price_hc'] = $price_hc;
                $value['net'] = $net;
                $value['net_hc'] = $net_hc;
                $value['rest_qty'] = $value['qty'];
                $value['unit_conversion'] = $konversiQty;
                $value['insertedBy'] = Auth::id();
                $value['updatedBy'] = Auth::id();
                $data->order_item()->create($value);
                if($totalQty == $purchase_item->qty){
                    $purchase_item->status = 1;
                }
                $purchase_item->rest_qty = $purchase_item->rest_qty - $nilai;
                $purchase_item->save();
            }
        }
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
                    'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
                    'supplier.currency:id,code,name',
                    'supplier.partner:id,code,name',
                    'kurs_type:id,name',
                    'currency:id,code,name',
                    'order_item',
                    'description:id,purchase_order_id,noted',
                    'order_item.purchase:id,no_pp',
                    'order_item.purchase_item:id,product_id,qty,rest_qty,unit',
                    'order_item.product:id,register_number,name,second_name',
                    'order_item.product.units:id,name,value',
                    'order_item.unit:id,name')
                ->when($search, function ($query) use ($search){
                    $query->where('no_op', 'LIKE',"%{$search}%");
                })
                ->limit(10)
                ->get();
        return response()->json(['data' => $data]);
    }

    public function getDataTtb(Request $request)
    {
        $search = $request->q;
    	$data = PurchaseOrder::with('branch:id,name',
                    'transaction_type:id,name',
                    'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
                    'supplier.currency:id,code,name',
                    'supplier.partner:id,code,name',
                    'supplier.product_status:id,supplier_id,product_status_id',
                    'kurs_type:id,name',
                    'currency:id,code,name',
                    'description:id,purchase_order_id,noted',
                    'order_item_ttb',
                    'order_item_ttb.purchase:id,no_pp',
                    'order_item_ttb.purchase_item:id,product_id,qty,rest_qty,unit',
                    'order_item_ttb.product:id,register_number,name,second_name',
                    'order_item_ttb.product.units',
                    'order_item_ttb.product.serial_number',
                    'order_item_ttb.unit:id,name')
                ->when($search, function ($query) use ($search){
                    $query->where('no_op', 'LIKE',"%{$search}%");
                })
                ->where('status',3)
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
                    'currency:id,code,name',
                    'order_item',
                    'description:id,purchase_order_id,noted',
                    'order_item.purchase:id,no_pp',
                    'order_item.purchase_item:id,product_id,qty,rest_qty,unit',
                    'order_item.product:id,register_number,name,second_name',
                    'order_item.product.units:id,name,value',
                    'order_item.unit:id,name')
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

    public function reject_approve(Request $request,$id)
    {
        $order = PurchaseOrder::find($id);
        $order->status = 2;
        $order->approved_by = Auth::id();
        $order->approved_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil direject']);
    }

    public function release(Request $request,$id)
    {
        $order = PurchaseOrder::find($id);
        $order->status = 3;
        $order->released_by = Auth::id();
        $order->released_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil direlease']);
    }

    public function reject_release(Request $request,$id)
    {
        $order = PurchaseOrder::find($id);
        $order->status = 4;
        $order->released_by = Auth::id();
        $order->released_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil direject']);
    }

    public function close(Request $request,$id)
    {
        $order = PurchaseOrder::find($id);
        $data = ['success'=>false, 'message' => 'Data tidak boleh diclose'];
        if($order->status != 7){
            $order->status = 5;
            $order->closed_by = Auth::id();
            $order->closed_at = now();
            $order->save();
            $data = ['success'=>true, 'message' => 'Data berhasil diclose'];
        }
        return response()->json($data);
    }
}
