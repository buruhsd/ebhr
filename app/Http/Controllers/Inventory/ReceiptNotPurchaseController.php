<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\BpbType;
use App\Models\Inventory\RequestItem;
use App\Models\Inventory\RequestItemDetail;
use App\Models\Inventory\ProductExpenditure;
use App\Models\Inventory\ReceiptNotPurchase;
use App\Models\Inventory\ReceiptNotPurchaseDetail;
use App\Models\Master\ProductSerialNumber;
use App\Models\Inventory\ProductExpenditureDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiptNotPurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:inventory', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $data = ReceiptNotPurchase::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'pbp_type:id,code,name',
            'expenditure:id,number_bpb,number_bpb as label,date_bpb',
            'original_warehouse:id,code,name',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
            'detail_items.product_status:id,name',
            'detail_items.expenditure_detail:id,unit_id,qty',
            'detail_items.expenditure_detail.unit:id,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->when($search, function ($query) use ($search){
                $query->where('number', 'LIKE',"%{$search}%");
            })
            ->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($data);
    }

    public function show($id)
    {
        $data = ReceiptNotPurchase::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'pbp_type:id,code,name',
            'expenditure:id,number_bpb,number_bpb as label,date_bpb',
            'original_warehouse:id,code,name',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
            'detail_items.product_status:id,name',
            'detail_items.expenditure_detail:id,qty',
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
            'pbp_type_id' => 'required|exists:pbp_types,id',
            'product_expenditure_id' => 'nullable|exists:product_expenditures,id',
            'original_warehouse_id' => 'nullable|exists:warehouses,id',
            'date' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.expenditure_detail_id' => 'nullable|distinct|exists:product_expenditure_details,id',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.product_status_id' => 'required|exists:product_statuses,id',
            'item.*.unit_id' => 'required|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1'
        ]);

        $request->merge([
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$savedata = ReceiptNotPurchase::create($request->all());
        if($request->product_expenditure_id){
            ProductExpenditure::find($request->product_expenditure_id)->update(['status'=>2]);
        }
        foreach($request->item as $value){
            $is_return = false;
            $is_serial_number = false;
            if($value['expenditure_detail_id']){
                $expenditure_detail = ProductExpenditureDetail::find($value['expenditure_detail_id']);
                $is_return = $expenditure_detail->is_return;
                $is_serial_number = $expenditure_detail->is_serial_number;
            }
            $value['is_serial_number'] = $is_serial_number;
            $value['is_return'] = $is_return;
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $savedata->detail_items()->create($value);

            $stock = StockCard::create([
                'trx_code' => $value->number_bpb,
                'trx_urut' => $savedata->id,
                'trx_date' => $request->date,
                'trx_jenis' => 'NP',
                'trx_dbcr' => 'D',
                'scu_code' => NULL,
                'scu_code_tipe' => NULL,
                'inv_code' => $value['no_register'],
                'loc_code' => $request->warehouse_id,
                'statusProduct' => $value['product_status_id'],
                'trx_kuan' => $value['qty'],
                'hargaSatuan' => 0,
                'trx_amnt' => 0,
                'trx_totl' => 0,
                'trx_hpok' => 0,
                'trx_havg' => 0,
                'pos_date' => '-',
                'sal_code' => '-'
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'pbp_type_id' => 'required|exists:pbp_types,id',
            'product_expenditure_id' => 'nullable|exists:product_expenditures,id',
            'original_warehouse_id' => 'nullable|exists:warehouses,id',
            'date' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.id' => 'nullable|distinct|exists:receipt_not_purchase_details,id',
            'item.*.expenditure_detail_id' => 'nullable|distinct|exists:product_expenditure_details,id',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.product_status_id' => 'required|exists:product_statuses,id',
            'item.*.unit_id' => 'required|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1'
        ]);

        $updateData = ReceiptNotPurchase::find($id);
        if(is_null($updateData)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        $request->merge([
            'updatedBy'=> Auth::id()
        ]);

        if($request->product_expenditure_id && $updateData->product_expenditure_id != $request->product_expenditure_id){
            ProductExpenditure::find($request->product_expenditure_id)->update(['status'=>2]);
            ProductExpenditure::find($updateData->product_expenditure_id)->update(['status'=>1]);
        }

    	$updateData->update($request->all());
        foreach($request->item as $value){
            if($value['id']){
                $detail = ReceiptNotPurchaseDetail::find($value['id']);
                $is_return = false;
                $is_serial_number = false;
                if($value['expenditure_detail_id']){
                    $expenditure_detail = ProductExpenditureDetail::find($value['expenditure_detail_id']);
                    $is_return = $expenditure_detail->is_return;
                    $is_serial_number = $expenditure_detail->is_serial_number;
                }
                $value['is_serial_number'] = $is_serial_number;
                $value['is_return'] = $is_return;
                $value['updatedBy'] = Auth::id();
                $detail->update($value);
            }else{
                $is_return = false;
                $is_serial_number = false;
                if($value['expenditure_detail_id']){
                    $expenditure_detail = ProductExpenditureDetail::find($value['expenditure_detail_id']);
                    $is_return = $expenditure_detail->is_return;
                    $is_serial_number = $expenditure_detail->is_serial_number;
                }
                $value['is_serial_number'] = $is_serial_number;
                $value['is_return'] = $is_return;
                $value['insertedBy'] = Auth::id();
                $value['updatedBy'] = Auth::id();
                $updateData->detail_items()->create($value);
            }
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        try {
            $data = ReceiptNotPurchase::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getAutoNumber(Request $request, $branch_id,$type_id)
    {
        $number = ReceiptNotPurchase::generateNumber($branch_id,$type_id);
        return response()->json(['data' => $number]);
    }

    public function get_data_serial(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = ReceiptNotPurchase::select('id','warehouse_id','number as label','date')->with(
                'warehouse:id,code,name',
                'detail_serial_items',
                'detail_serial_items.product:id,register_number,name,second_name',
                'detail_serial_items.product_status:id,name',
                'detail_serial_items.unit:id,name',
            )->has('detail_serial_items')
            ->when($branch, function ($query) use ($branch){
                $query->where('branch_id', $branch);
            })
            ->when($search, function ($query) use ($search){
                $query->where('number', 'LIKE',"%{$search}%");
            })->get();
        return response()->json(['data' => $data]);
    }

    public function get_data_return(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = ReceiptNotPurchase::select('id','warehouse_id','number as label','date')->with(
                'warehouse:id,code,name',
                'detail_return_items',
                'detail_return_items.product:id,register_number,name,second_name',
                'detail_return_items.product_status:id,name',
                'detail_return_items.unit:id,name',
            )->has('detail_return_items')
            ->when($branch, function ($query) use ($branch){
                $query->where('branch_id', $branch);
            })
            ->when($search, function ($query) use ($search){
                $query->where('number', 'LIKE',"%{$search}%");
            })->get();
        return response()->json(['data' => $data]);
    }
}
