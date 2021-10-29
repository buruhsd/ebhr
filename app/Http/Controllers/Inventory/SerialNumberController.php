<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use App\Models\Inventory\Receipt;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Inventory\ReceiptItems;
use App\Models\Inventory\SerialNumber;
use App\Models\Inventory\SerialNumberDetail;
use App\Models\Inventory\ProductExpenditure;
use App\Models\Inventory\ProductExpenditureDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SerialNumberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $data = SerialNumber::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'details',
            'receipt',
            'receipt_item',
            'receipt_item.unit_ttb',
            'product',
            'product_status',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->when($search, function ($query) use ($search){
                $query->whereHas('receipt', function ($q) use ($search){
                    $q->where('receipts.number',$search);
                });
            })
            ->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($data);
    }

    public function show($id)
    {
        $data = SerialNumber::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'details',
            'receipt',
            'receipt_item',
            'product',
            'product_status',
            'insertedBy:id,name',
            'updatedBy:id,name')->find($id);
        if($data){
            return response()->json(['success' => true, 'data' => $data]);
        }
        return response()->json(['success' => false, 'message' => 'Data tidak ada']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receipt_id' => 'required|exists:receipts,id',
            'receipt_item_id' => 'required|exists:receipt_items,id',
            'product_id' => 'required|exists:products,id',
            'product_status_id' => 'required|exists:product_statuses,id',
            'item' => 'required',
            'item.*.no_seri' => 'required|string|distinct|unique:serial_number_details,no_seri',
        ]);

        $receiptItems = ReceiptItems::find($request->receipt_item_id);
        $qty = $receiptItems->qty;
        $total_item = count($request->item);
        if($qty != $total_item){
            return response()->json(['success' => false, 'message' => 'Jumlah Nomor seri harus sama dengan jumlah qty TTB']);
        }

        $request->merge([
            'dk' => 'D',
            'type' => 'TTB',
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$saveData = SerialNumber::create($request->all());
        foreach($request->item as $value){
            $value['no_seri'] = strtoupper($value['no_seri']);
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $saveData->details()->create($value);
        }
        $receiptItems->status = 1;
        $receiptItems->save();

        $check_receipt = Receipt::select('id','purchase_order_id','status')
            ->has('receipt_items_serial')
            ->where('id',$request->receipt_id)->first();
        if(is_null($check_receipt)){
            $receipt = Receipt::select('id','purchase_order_id','status')
                ->where('id',$request->receipt_id)->first();
            $receipt->status = 1;
            $receipt->save();
            PurchaseOrder::find($receipt->purchase_order_id)->update(['status'=>7]);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function storeBpb(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_expenditure_id' => 'required|exists:product_expenditures,id',
            'product_expenditure_detail_id' => 'required|exists:product_expenditure_details,id',
            'product_id' => 'required|exists:products,id',
            'product_status_id' => 'required|exists:product_statuses,id',
            'item' => 'required',
            'item.*.no_seri' => 'required|distinct|unique:serial_number_details,no_seri,null,id,no_seri,serial_number_id',
        ]);

        $receiptItems = ProductExpenditureDetail::find($request->product_expenditure_detail_id);
        $qty = $receiptItems->qty;
        $total_item = count($request->item);
        if($qty != $total_item){
            return response()->json(['success' => false, 'message' => 'Jumlah Nomor seri harus sama dengan jumlah qty BPB']);
        }

        $request->merge([
            'dk' => 'K',
            'type' => 'BPB',
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$saveData = SerialNumber::create($request->all());
        foreach($request->item as $value){
            $number = SerialNumberDetail::find($value['no_seri']);
            $value['no_seri'] = $number->no_seri;
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $saveData->details()->create($value);
            $number->update(['status' => 1]);
        }
        $receiptItems->status = 1;
        $receiptItems->save();

        $check_receipt = ProductExpenditure::select('id','status')
            ->has('detail_serial_items')
            ->where('id',$request->product_expenditure_id)->first();
        if(is_null($check_receipt)){
            $receipt = ProductExpenditure::select('id','status')
                ->where('id',$request->product_expenditure_id)->first();
            $receipt->status = 1;
            $receipt->save();
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receipt_id' => 'required|exists:receipts,id',
            'receipt_item_id' => 'required|exists:receipt_items,id',
            'product_id' => 'required|exists:products,id',
            'product_status_id' => 'required|exists:product_statuses,id',
            'item' => 'required',
            'item.*.no_seri' => 'required|string|distinct|unique:serial_number_details,no_seri',
        ]);

        $updateData = SerialNumber::find($id);
        if(is_null($updateData)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        $receiptItems = ReceiptItems::find($request->receipt_item_id);
        $qty = $receiptItems->qty;
        $total_item = count($request->item);
        if($qty != $total_item){
            return response()->json(['success' => false, 'message' => 'Jumlah Nomor seri harus sama dengan jumlah qty TTB']);
        }

        $request->merge([
            'updatedBy'=> Auth::id()
        ]);

    	$updateData->update($request->all());
        $updateData->details()->delete();
        foreach($request->item as $value){
            $value['no_seri'] = strtoupper($value['no_seri']);
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $updateData->details()->create($value);
        }
        $receiptItems->status = 1;
        $receiptItems->save();

        $check_receipt = Receipt::select('id','purchase_order_id','status')
            ->has('receipt_items_serial')
            ->where('id',$request->receipt_id)->first();
        if(is_null($check_receipt)){
            $receipt = Receipt::select('id','purchase_order_id','status')
                ->where('id',$request->receipt_id)->first();
            $receipt->status = 1;
            $receipt->save();
            PurchaseOrder::find($receipt->purchase_order_id)->update(['status'=>7]);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function getDataNoseri(Request $request,$type)
    {
        $search = $request->search;
        $data = SerialNumberDetail::with(
            'serial_number',
            'serial_number.branch:id,code,name',
            'serial_number.warehouse:id,code,name',
            'serial_number.receipt:id,number,date',
            'serial_number.product_expenditure:id,number_bpb,date_bpb',
            'serial_number.product:id,register_number,name,second_name',
            'serial_number.product_status:id,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->when($search, function ($query) use ($search){
                $query->whereHas('serial_number', function ($quer) use ($search){
                    $quer->whereHas('receipt', function ($que) use ($search){
                        $que->where('receipts.number','LIKE',"%{$search}%");
                    });
                })->orWhere('no_seri','LIKE',"{$search}%");
            })
            ->whereHas('serial_number', function ($query) use ($type){
                $query->where('serial_numbers.type',$type);
            })
            ->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($data);
    }

    public function updateNoseri(Request $request, $id)
    {
        $this->validate($request, [
            "no_seri" => "required|exists:serial_number_details,id"
        ]);

        $updateData = SerialNumberDetail::find($id);
        if(is_null($updateData)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }
        $old_number = $updateData->no_seri;
        $number = SerialNumberDetail::find($request->no_seri);
        $request->merge([
            'no_seri'=> $number->no_seri,
            'updatedBy'=> Auth::id()
        ]);
        $number->status = 1;
        $number->save();
        SerialNumberDetail::where(['no_seri'=> $old_number,'status' => 1])
                ->whereHas('serial_number', function ($query){
                    $query->where('serial_numbers.dk','D');
                })
                ->update(['status' => 0]);

    	$updateData->update($request->all());
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        try {
            $data = SerialNumber::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getDataNoseriByProduct(Request $request,$product_id)
    {
        $search = $request->search;
        $data = SerialNumberDetail::select('id','serial_number_id','no_seri as label')
            ->where('status',0)
            ->when($search, function ($query) use ($search){
                $query->where('no_seri','LIKE',"{$search}%");
            })
            ->whereHas('serial_number', function ($query) use ($product_id){
                $query->where('serial_numbers.product_id',$product_id);
            })
            ->whereHas('serial_number', function ($query){
                $query->where('serial_numbers.type','!=','BPB');
            })
            ->orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }
}
