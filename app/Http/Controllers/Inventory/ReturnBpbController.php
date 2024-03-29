<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\Inventory\ReturnBpb;
use App\Models\Inventory\ReturnBpbDetail;
use App\Models\Inventory\ProductExpenditure;
use App\Models\Inventory\ProductExpenditureDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReturnBpbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:inventory', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $data = ReturnBpb::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'product_expenditure:id,date_bpb,number_bpb as label',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
            'detail_items.product_status:id,name',
            'detail_items.product_expenditure_detail',
            'detail_items.product_expenditure_detail.product:id,register_number,name,second_name,product_number',
            'detail_items.product_expenditure_detail.product_status:id,name',
            'detail_items.product_expenditure_detail.unit:id,name',
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
        $data = ReturnBpb::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
            'detail_items.product_status:id,name',
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
            'product_expenditure_id' => 'required|exists:product_expenditures,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.product_expenditure_detail_id' => 'required|exists:product_expenditure_details,id',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.product_status_id' => 'required|exists:product_statuses,id',
            'item.*.unit_id' => 'required|distinct|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1',
            'item.*.price' => 'nullable|numeric'
        ]);
        $is_return = [];
        foreach($request->item as $value){
            $check_data = ProductExpenditureDetail::find($value['product_expenditure_detail_id']);
            if($value['qty'] > $check_data->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah RDT tidak boleh melebihi dari jumlah BPB']);
            }
            if($value['qty'] == $check_data->qty){
                array_push($is_return,$value['product_expenditure_detail_id']);
            }

            $stock = StockCard::create([
                'trx_code' => $productExpenditure->number_bpb,
                'trx_urut' => $productExpenditure->id,
                'trx_date' => $productExpenditure->date_bpb,
                'trx_jenis' => 'BK',
                'trx_dbcr' => 'D',
                'scu_code' => NULL,
                'scu_code_tipe' => NULL,
                'inv_code' => $value['no_register'],
                'loc_code' => $request->warehouse_id,
                'statusProduct' => $item['product_status_id'],
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

        $request->merge([
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$saveData = ReturnBpb::create($request->all());
        foreach($request->item as $value){
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $saveData->detail_items()->create($value);
        }

        $is_return_bpb = true;
        if(count($request->item) == count($is_return)){
            $is_return_bpb = true;
        }
        ProductExpenditure::find($request->product_expenditure_id)->update(['is_return_bpb'=>$is_return_bpb]);
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'product_expenditure_id' => 'required|exists:product_expenditures,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.product_expenditure_detail_id' => 'required|exists:product_expenditure_details,id',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.product_status_id' => 'required|exists:product_statuses,id',
            'item.*.unit_id' => 'required|distinct|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1',
            'item.*.price' => 'nullable|numeric'
        ]);

        $updateData = ReturnBpb::find($id);
        if(is_null($updateData)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        $is_return = [];
        foreach($request->item as $value){
            $check_data = ProductExpenditureDetail::find($value['product_expenditure_detail_id']);
            if($value['qty'] > $check_data->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah RDT tidak boleh melebihi dari jumlah BPB']);
            }
            if($value['qty'] == $check_data->qty){
                array_push($is_return,$value['product_expenditure_detail_id']);
            }
        }

        $request->merge([
            'updatedBy'=> Auth::id()
        ]);

    	$updateData->update($request->all());
        $updateData->detail_items()->delete();
        foreach($request->item as $value){
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $updateData->detail_items()->create($value);
        }

        $is_return_bpb = true;
        if(count($request->item) == count($is_return)){
            $is_return_bpb = true;
        }
        ProductExpenditure::find($request->product_expenditure_id)->update(['is_return_bpb'=>$is_return_bpb]);
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        try {
            $data = ReturnBpb::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getAutoNumber(Request $request, $branch_id)
    {
        $number = ReturnBpb::generateNumber($branch_id);
        return response()->json(['data' => $number]);
    }
}
