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
        $data = Receipt::with('receipt_items')->orderBy('created_id', 'desc')->paginate(10);
    }

    public function store(Request $request){
        $this->validate($request, [
            'branch_id' => 'required',
            'warehouse_id' => 'required',
            'purchase_order_id' => 'required',
            'supplier_id' => 'required',
            'date' => 'required',
            'noted',
            'item' => 'required|array',
            'item.*.purchase_order_item_id' => 'required|distinct|exists:purchase_order_items,id',
            'item.*.unit_id' => 'required|numeric|exists:units,id',
            'item.*.qty' => 'required||min:1',
            'item.*.price_idr' => 'nullable',
            'item.*.price_valas' => 'required',
            'item.*.discount' => 'required|numeric',
            // 'number', 'generated
            // 'ppn', 'generated from op
            // 'term_of_payment', 'generated from supplier
            // 'currency_id','generated from op
            // 'kurs_type_id','generated from op
            // 'kurs','generated from op
            // 'ppn_valas', 'generated from op
            // 'ppn_idr', 'generated from op
            // 'total_valas', 'hitungan dari array items'
            // 'total_idr', 'hitungan dari array items'
            // 'dpp', 'total idr yg di round 1000'
            // 'status' default 0,
            // 'insertedBy' auth,
            // 'updatedBy' auth,
        ]);
        $op = PurchaseOrder::find($request->purchase_order_id);
        $total_valas = 0;
        $total_idr = 0;
        $arrItem = [];
        foreach($request->item as $item){
            $po_item = PurchaseOrderItem::find($item['purchase_order_item_id']);
            $total_valas += $po_item->price * $item['qty'];
            $total_idr += $po_item->price_hc * $item['qty'];
            $item['unit_op_id'] = $po_item->unit_id;
            $po_item->product->units()->where('type', 'extern')->first()->id;
            $item['unit_id'] = $po_item->product->units()->where('type', 'extern')->first()->unit_id;
            $item['unit_conversion'] = $po_item->product->units()->where('type', 'extern')->first()->value;
            $item['insertedBy'] = Auth::User()->id;
            $item['updatedBy'] = Auth::User()->id;
            array_push($arrItem, $item);
        }

        $term_of_payment = Supplier::find($request->supplier_id)->term_of_payment;

        $dpp = round($total_idr,0);
        $dpp = intval(substr_replace($dpp,"000",-3));
        $payload = array_merge($request->all(),[
            'ppn' => $op->ppn,
            'term_of_payment' => $term_of_payment,
            'currency_id' => $op->currency_id,
            'kurs_type_id' => $op->kurs_type_id,
            'kurs' => $op->kurs,
            'ppn_valas' => $op->ppn,
            'ppn_idr' => $op->ppn_hc,
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
        }

        return response()->json(['data' => $receipt]);

    }
}
