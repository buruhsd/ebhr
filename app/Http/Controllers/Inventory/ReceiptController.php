<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use App\Models\Kurs;
use App\Models\Supplier;
use App\Models\Currency;
use App\Helpers\DebtCard;
use App\Models\StockCard;
use App\Models\Inventory\Receipt;
use App\Models\Inventory\ReceiptItems;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Purchase\PurchaseLetter;
use App\Models\Purchase\PurchaseLetterItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:inventory');
    }

    public function index(Request $request){
        $data = Receipt::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'supplier.partner:id,code,name',
            'kurs_type:id,name',
            'currency:id,code,name',
            'purchase_order',
            'receipt_items',
            'receipt_items.purchase_order_item.product',
            'receipt_items.purchase_order_item.product.serial_number',
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

        DB::beginTransaction();
        try {
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
                $is_return = false;
                $is_serial_number = false;
                $serial = $po_item->product->serial_number;
                if($serial){
                    $is_return = $serial->is_return;
                    $is_serial_number = $serial->is_serial_number;
                }
                $total_valas += $net * $item['qty'];
                $total_idr += $net_hc * $item['qty'];
                $item['product_id'] = $po_item->product_id;
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
                $item['is_serial_number'] = $is_serial_number;
                $item['is_return'] = $is_return;
                $item['insertedBy'] = Auth::id();
                $item['updatedBy'] = Auth::id();
                array_push($arrItem, $item);

            }

            $supplier = Supplier::find($supplier_id);
            $term_of_payment = $supplier->term_of_payment;
            $dpp = $total_idr;
            // $dpp = intval(substr_replace($dpp,"000",-3));
            $ppn = $op->ppn;
            if($ppn < 0){
                $ppn = ($ppn * -1) + 100;
            }
            $ppn_idr = $dpp * ($ppn/100);
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

                $stock = StockCard::create([
                    'trx_code' => $receipt->number,
                    'trx_urut' => $receipt->id,
                    'trx_date' => $receipt->date,
                    'trx_jenis' => 'BP',
                    'trx_dbcr' => 'D',
                    'scu_code' => $receipt->supplier_id,
                    'scu_code_tipe' => Supplier::class,
                    'inv_code' => $item['no_register'],
                    'loc_code' => $receipt->warehouse_id,
                    'statusProduct' => $item['product_status_id'],
                    'trx_kuan' => $item['qty'],
                    'hargaSatuan' => $po_item->price_hc,
                    'trx_amnt' => $po_item->price_hc * $item['qty'],
                    'trx_totl' => 0,
                    'trx_hpok' => 0,
                    'trx_havg' => 0,
                    'pos_date' => '-',
                    'sal_code' => '-'
                ]);
            }
            $total_qty_op = PurchaseOrderItem::where('purchase_order_id',$request->purchase_order_id)->sum(DB::raw('qty * unit_conversion'));
            $total_qty_ttb = DB::table('receipts')
                        ->join('receipt_items', 'receipt_items.receipt_id', '=', 'receipts.id')
                        ->where('receipts.purchase_order_id', $request->purchase_order_id)
                        ->sum(DB::raw('receipt_items.qty'));
            if($total_qty_ttb == $total_qty_op){
                $op->status = 6;
            }
            $chekc_receipt = Receipt::select('id','status')
                ->has('receipt_items_serial')
                ->where('id',$receipt->id)->first();
            if(is_null($chekc_receipt)){
                $op->status = 7;
            }
            $op->save();

            $currency_code = '';
            $currency = Currency::find($receipt->currency_id);
            if($currency){
                $currency_code = $currency->code;
            }

            $debtCard = new DebtCard();
            $payloadCard = [
                "item_id" => $receipt->id,
                "supplier_id" => $receipt->supplier_id,
                "supplier_code" => $supplier->partner->code,
                "supplier_name" => $supplier->partner->name,
                "trx_code" => $receipt->number,
                "trx_date" => $receipt->date,
                "trx_due_date" => date('Y-m-d', strtotime($receipt->date. ' + '.$term_of_payment.' days')),
                "proof_code" => $receipt->number,
                "proof_date" => $receipt->date,
                "debit_kredit" => "D",
                "type" => "LP",
                "group" => "P1",
                "amount_trx_fc" => $receipt->total_idr,
                "amount_trx_hc" => $receipt->total_valas,
                "amount_proof_fc" => $receipt->total_idr,
                "amount_proof_hc" => $receipt->total_valas,
                "kurs" => $receipt->kurs,
                "currency" => $currency_code,
                "description" => $receipt->noted,
                "source" => "TTB"
            ];
            $response = $debtCard->save($payloadCard);
            if(!$response['success']){
                Log::error($response);
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'Gagal menambahkan data penerimaan barang']);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil menambahkan data penerimaan barang']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan data penerimaan barang']);
        }
    }
    // noted masih belum bener
    public function update(Request $request,$id){
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

        DB::beginTransaction();
        try {
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
                $is_return = false;
                $is_serial_number = false;
                $serial = $po_item->product->serial_number;
                if($serial){
                    $is_return = $serial->is_return;
                    $is_serial_number = $serial->is_serial_number;
                }
                $total_valas += $net * $item['qty'];
                $total_idr += $net_hc * $item['qty'];
                $item['product_id'] = $po_item->product_id;
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
                $item['is_serial_number'] = $is_serial_number;
                $item['is_return'] = $is_return;
                $item['insertedBy'] = Auth::id();
                $item['updatedBy'] = Auth::id();
                array_push($arrItem, $item);

            }

            $supplier = Supplier::find($supplier_id);
            $term_of_payment = $supplier->term_of_payment;
            $dpp = $total_idr;
            // $dpp = intval(substr_replace($dpp,"000",-3));
            $ppn = $op->ppn;
            if($ppn < 0){
                $ppn = ($ppn * -1) + 100;
            }
            $ppn_idr = $dpp * ($ppn/100);
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
                'updatedBy' => Auth::User()->id,
            ]);

            $receipt = Receipt::find($id)->update($payload);
            $receipt->receipt_items()->delete();
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
            }
            $chekc_receipt = Receipt::select('id','status')
                ->has('receipt_items_serial')
                ->where('id',$receipt->id)->first();
            if(is_null($chekc_receipt)){
                $op->status = 7;
            }
            $op->save();

            $currency_code = '';
            $currency = Currency::find($receipt->currency_id);
            if($currency){
                $currency_code = $currency->code;
            }

            $debtCard = new DebtCard();
            $payloadCard = [
                "item_id" => $receipt->id,
                "supplier_id" => $receipt->supplier_id,
                "supplier_code" => $supplier->partner->code,
                "supplier_name" => $supplier->partner->name,
                "trx_code" => $receipt->number,
                "trx_date" => $receipt->date,
                "trx_due_date" => date('Y-m-d', strtotime($receipt->date. ' + '.$term_of_payment.' days')),
                "proof_code" => $receipt->number,
                "proof_date" => $receipt->date,
                "debit_kredit" => "D",
                "type" => "LP",
                "group" => "P1",
                "amount_trx_fc" => $receipt->total_idr,
                "amount_trx_hc" => $receipt->total_valas,
                "amount_proof_fc" => $receipt->total_idr,
                "amount_proof_hc" => $receipt->total_valas,
                "kurs" => $receipt->kurs,
                "currency" => $currency_code,
                "description" => $receipt->noted,
                "source" => "TTB"
            ];
            $response = $debtCard->update($id,$payloadCard);
            if(!$response['success']){
                Log::error($response);
                DB::rollback();
                return response()->json(['success' => false, 'message' => 'Gagal memperbaharui data penerimaan barang']);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil memperbaharui data penerimaan barang']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Gagal memperbaharui data penerimaan barang']);
        }
    }

    public function getNumberReceipt(Request $request, $id)
    {
        $number = Receipt::generateNumber($id);
        return response()->json(['data' => $number]);
    }

    public function product_serial_number(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = Receipt::select('id','warehouse_id','number as label','date')->with(
                'warehouse:id,code,name',
                'receipt_items_serial:id,receipt_id,product_id,product_status_id,unit_id,qty',
                'receipt_items_serial.product:id,register_number,name,second_name',
                'receipt_items_serial.product_status:id,code,name',
                'receipt_items_serial.unit_ttb:id,code,name',
            )->has('receipt_items_serial')
            ->where('status',0)
            ->when($branch, function ($query) use ($branch){
                $query->where('branch_id', $branch);
            })
            ->when($search, function ($query) use ($search){
                $query->where('number', 'LIKE',"%{$search}%");
            })->get();
        return response()->json(['data' => $data]);
    }

    public function get_items_by_product(Request $request,$product_id)
    {
        $data = ReceiptItems::with('receipt:id,supplier_id,number,date',
                'receipt.supplier.partner:id,code,name','unit_op:id,name')
                ->where('product_id',$product_id)->get();
        return response()->json(['data' => $data]);
    }
}
