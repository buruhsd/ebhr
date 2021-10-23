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
use App\Models\Master\ProductSerialNumber;
use App\Models\Inventory\ProductExpenditureDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductExpenditureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request){
        $search = $request->search;
        $data = ProductExpenditure::with(
            'request_item:id,bpb_type_id,number_spb,date_spb',
            'request_item.bpb_type:id,code,name,is_warehouse,is_number_pkb',
            'branch:id,code,name',
            'warehouse:id,code,name',
            'destination_warehouse:id,code,name',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
            'detail_items.product_status:id,name',
            'detail_items.request_item_detail.unit:id,code,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->when($search, function ($query) use ($search){
                $query->where('number_bpb', 'LIKE',"%{$search}%");
            })
            ->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($data);
    }

    public function show($id)
    {
        $data = ProductExpenditure::with(
            'request_item:id,number_spb,date_spb',
            'branch:id,code,name',
            'warehouse:id,code,name',
            'destination_warehouse:id,code,name',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
            'detail_items.product_status:id,name',
            'detail_items.request_item_detail.unit:id,code,name',
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
            'request_item_id' => 'required|exists:request_items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'nullable|exists:warehouses,id',
            'date_bpb' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.request_item_detail_id' => 'required|distinct|exists:request_item_details,id',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.product_status_id' => 'required|exists:product_statuses,id',
            'item.*.unit_id' => 'required|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1',
            'item.*.is_serial_number' => 'required'
        ]);
        foreach($request->item as $value){
            $requestItemDetail = RequestItemDetail::find($value['request_item_detail_id']);
            if($value['qty'] > $requestItemDetail->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah BPB tidak boleh melebihi dari jumlah SPB']);
            }
        }

        $bpb_type_id = RequestItem::find($request->request_item_id)->bpb_type_id;
        $request->merge([
            'bpb_type_id'=> $bpb_type_id,
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$productExpenditure = ProductExpenditure::create($request->all());
        foreach($request->item as $value){
            $is_return = false;
            $product = ProductSerialNumber::where('product_id',$value['product_id'])->first();
            if($product){
                $is_return = $product->is_return;
            }
            $value['is_return'] = $is_return;
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $productExpenditure->detail_items()->create($value);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'request_item_id' => 'required|exists:request_items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'nullable|exists:warehouses,id',
            'date_bpb' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.request_item_detail_id' => 'required|distinct|exists:request_item_details,id',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.product_status_id' => 'required|exists:product_statuses,id',
            'item.*.unit_id' => 'required|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1',
            'item.*.is_serial_number' => 'required'
        ]);

        $productExpenditure = ProductExpenditure::find($id);
        if(is_null($productExpenditure)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        foreach($request->item as $value){
            $requestItemDetail = RequestItemDetail::find($value['request_item_detail_id']);
            if($value['qty'] > $requestItemDetail->qty){
                return response()->json(['success' => false, 'message' => 'Jumlah BPB tidak boleh melebihi dari jumlah SPB']);
            }
        }

        $bpb_type_id = RequestItem::find($request->request_item_id)->bpb_type_id;
        $request->merge([
            'bpb_type_id'=> $bpb_type_id,
            'updatedBy'=> Auth::id()
        ]);

    	$productExpenditure->update($request->all());
        $productExpenditure->detail_items()->delete();
        foreach($request->item as $value){
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $productExpenditure->detail_items()->create($value);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        try {
            $data = ProductExpenditure::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getAutoNumber(Request $request, $branch_id,$type_id)
    {
        $number = ProductExpenditure::generateNumber($branch_id,$type_id);
        return response()->json(['data' => $number]);
    }

    public function get_data_serial(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = ProductExpenditure::select('id','warehouse_id','number_bpb as label','date_bpb')->with(
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
                $query->where('number_bpb', 'LIKE',"%{$search}%");
            })->get();
        return response()->json(['data' => $data]);
    }

    public function get_data_return(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = ProductExpenditure::select('id','warehouse_id','number_bpb as label','date_bpb')->with(
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
                $query->where('number_bpb', 'LIKE',"%{$search}%");
            })->get();
        return response()->json(['data' => $data]);
    }
}
