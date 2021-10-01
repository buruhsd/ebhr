<?php

namespace App\Http\Controllers\Purchase;


use DB;
use Auth;
use App\Models\Kurs;
use App\Models\Supplier;
use App\Models\Purchase\Receipt;
use App\Models\Purchase\ReceiptItems;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Purchase\PurchaseLetter;
use App\Models\Purchase\PurchaseLetterItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(){
        $data = Receipt::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'supplier.partner:id,code,name',
            'kurs_type:id,name',
            'currency:id,code,name',
            'purchase_order',
            'receipt_items',
            'receipt_items.purchase_order_item.product',
            'receipt_items.unit_ttb:id,name',
            'receipt_items.unit_op:id,name',
            'receipt_items.product_status:id,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($data);
    }

    public function store(Request $request){
        $this->validate($request, [
            'branch_id' => 'required',
            'warehouse_id' => 'required',
            'purchase_order_id' => 'required',
            'date' => 'required',
            'noted' => 'required',
            'item' => 'required|array',
            'item.*.purchase_order_item_id' => 'required|distinct|exists:purchase_order_items,id',
            'item.*.unit_id' => 'required|numeric|exists:units,id',
            'item.*.qty' => 'required|min:1',
            'item.*.product_status_id' => 'required',
        ]);
        $op = PurchaseOrder::find($request->purchase_order_id);
        $kurs = $op->kurs;
        $supplier_id = $op->supplier_id;
        $total_valas = 0;
        $total_idr = 0;
        $arrItem = [];
        foreach($request->item as $item){
            $po_item = PurchaseOrderItem::find($item['purchase_order_item_id']);
            $total_qty = $po_item->unit_conversion * $po_item->rest_qty;
            $item['total_qty'] = $total_qty;
            if($item['qty'] > $total_qty){
                return response()->json(['success' => false, 'message' => 'Jumlah TTB tidak boleh melebihi dari jumlah OP']);
            }
            $net = $po_item->net;
            $net_hc = $po_item->net_hc;
            if($po_item->unit_conversion > 1){
                $net = $po_item->net/$po_item->unit_conversion;
                $net_hc = $po_item->net_hc/$po_item->unit_conversion;
            }
            $total_valas += $net * $item['qty'];
            $total_idr += $net_hc * $item['qty'];
            $item['unit_op_id'] = $po_item->unit_id;
            $item['qty_op'] = $po_item->qty;
            $item['unit_id'] = $po_item->product->units()->where('type', 'extern')->first()->unit_id;
            $item['unit_conversion'] = $po_item->unit_conversion;
            $item['price_valas'] = $po_item->price;
            $item['price_idr'] = $po_item->price_hc;
            $item['kurs'] = $kurs;
            $item['discount'] = $po_item->discount;
            $item['net_valas'] = $po_item->net;
            $item['net_idr'] = $po_item->net_hc;
            $item['insertedBy'] = Auth::id();
            $item['updatedBy'] = Auth::id();
            array_push($arrItem, $item);
        }

        $term_of_payment = Supplier::find($supplier_id)->term_of_payment;
        $dpp = round($total_idr,0);
        $dpp = intval(substr_replace($dpp,"000",-3));
        $ppn_idr = $dpp * ($op->ppn/100);
        $payload = array_merge($request->all(),[
            'supplier_id' => $supplier_id,
            'ppn' => $op->ppn,
            'term_of_payment' => $term_of_payment,
            'currency_id' => $op->currency_id,
            'kurs_type_id' => $op->kurs_type_id,
            'kurs' => $op->kurs,
            'ppn_valas' => $op->kurs > 0 ? $op->ppn_hc/$op->kurs : 0,
            'ppn_idr' => $ppn_idr,
            'total_valas' => $total_valas,
            'total_idr' => $total_idr,
            'dpp'=> $dpp,
            'status' => 0,
            'insertedBy' => Auth::User()->id,
            'updatedBy' => Auth::User()->id,
        ]);

        $receipt = Receipt::create($payload);
        foreach($arrItem as $item){
            $receipt->receipt_items()->create($item);
            $po_item = PurchaseOrderItem::find($item['purchase_order_item_id']);
            if($item['total_qty'] == $item['qty']){
                $po_item->update(['status'=>1]);
            }
            $qty = $item['qty'];
            if($po_item->unit_conversion > 1){
                $qty = $item['qty']/$po_item->unit_conversion;
            }
            $po_item->rest_qty = $po_item->rest_qty - $qty;
            $po_item->save();
        }
        $total_qty_op = PurchaseOrderItem::where('purchase_order_id',$request->purchase_order_id)->sum(DB::raw('qty * unit_conversion'));
        $total_qty_ttb = DB::table('receipts')
                    ->join('receipt_items', 'receipt_items.receipt_id', '=', 'receipts.id')
                    ->where('receipts.purchase_order_id', $request->purchase_order_id)
                    ->sum(DB::raw('receipt_items.qty'));
        if($total_qty_ttb == $total_qty_op){
            $op->status = 6;
            $op->save();
        }
        return response()->json(['success' => true, 'message' => 'Berhasil menambahkan data penerimaan barang']);
    }

    public function getNumberReceipt(Request $request, $id)
    {
        $number = Receipt::generateNumber($id);
        return response()->json(['data' => $number]);
    }
}
