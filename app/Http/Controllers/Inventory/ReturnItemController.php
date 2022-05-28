<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\Inventory\ReturnItem;
use App\Models\Inventory\ReturnItemDetail;
use App\Models\Inventory\ProductExpenditureDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReturnItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:inventory');
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $data = ReturnItem::with(
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
        $data = ReturnItem::with(
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
            'item.*.note' => 'required'
        ]);
        foreach($request->item as $value){
            $check_data = ProductExpenditureDetail::find($value['product_expenditure_detail_id']);
            if($value['qty'] > $check_data->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah KG tidak boleh melebihi dari jumlah BPB']);
            }
        }

        $request->merge([
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$saveData = ReturnItem::create($request->all());
        foreach($request->item as $value){
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $saveData->detail_items()->create($value);
        }
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
            'item.*.note' => 'required'
        ]);

        $updateData = ReturnItem::find($id);
        if(is_null($updateData)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        foreach($request->item as $value){
            $check_data = ProductExpenditureDetail::find($value['product_expenditure_detail_id']);
            if($value['qty'] > $check_data->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah KG tidak boleh melebihi dari jumlah BPB']);
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
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        try {
            $data = ReturnItem::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getAutoNumber(Request $request, $branch_id)
    {
        $number = ReturnItem::generateNumber($branch_id);
        return response()->json(['data' => $number]);
    }
}
