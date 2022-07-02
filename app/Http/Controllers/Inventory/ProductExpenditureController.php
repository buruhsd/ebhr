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
        $this->middleware('permission:inventory', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    }

    public function index(Request $request){
        $search = $request->search;
        $data = ProductExpenditure::with(
            'request_item:id,bpb_type_id,user_unit_id,number_spb,date_spb',
            'request_item.bpb_type:id,code,name,is_warehouse,is_number_pkb',
            'request_item.user_unit:id,code,name',
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

        $requestItem = RequestItem::find($request->request_item_id);
        $request->merge([
            'bpb_type_id'=> $requestItem->bpb_type_id,
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

            $requestItemDetail = RequestItemDetail::find($value['request_item_detail_id']);
            if($value['qty'] == $requestItemDetail->rest_qty){
                $requestItemDetail->status = 1;
            }
            $requestItemDetail->rest_qty = $requestItemDetail->rest_qty - $value['qty'];
            $requestItemDetail->save();

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
        $check = RequestItemDetail::where(['request_item_id'=>$request->request_item_id])->sum('rest_qty');
        if($check == 0){
            $requestItem->update(['status'=>4]);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id)
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

        $requestItem = RequestItem::find($request->request_item_id);
        $request->merge([
            'bpb_type_id'=> $requestItem->bpb_type_id,
            'updatedBy'=> Auth::id()
        ]);

        if($productExpenditure->request_item_id != $request->request_item_id){
            RequestItem::find($productExpenditure->request_item_id)->update(['status'=>1]);
        }

    	$productExpenditure->update($request->all());
        // $productExpenditure->detail_items()->delete();
        foreach($request->item as $value){
            $detail = ProductExpenditureDetail::find($value['id']);
            if($detail){
                $value['insertedBy'] = Auth::id();
                $value['updatedBy'] = Auth::id();
                $detail->update($value);

                $itemDetail = RequestItemDetail::find($value['request_item_detail_id']);
                $usedQty = ProductExpenditureDetail::where('request_item_detail_id',$value['request_item_detail_id'])->sum('qty');
                $status = 0;
                if(($itemDetail->qty - $usedQty) == 0){
                    $status = 1;
                }
                $itemDetail->rest_qty = $itemDetail->qty - $usedQty;
                $itemDetail->status = $status;
                $itemDetail->save();
            }
        }

        $check = RequestItemDetail::where(['request_item_id'=>$requestItem->id])->sum('rest_qty');
        if($check == 0){
            $requestItem->update(['status'=>4]);
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

    public function getData(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = ProductExpenditure::select('id','number_bpb','number_bpb as label', 'date_bpb','destination_warehouse_id')->with(
                'destination_warehouse:id,code,name',
                'detail_items',
                'detail_items.product:id,register_number,name,second_name,product_number',
                'detail_items.product.serial_number:id,product_id,is_serial_number',
                'detail_items.unit:id,name',
                'detail_items.product_status:id,name')
            ->where('status',1)
            ->when($branch, function ($query) use ($branch){
                $query->where('branch_id', $branch);
            })
            ->when($search, function ($query) use ($search){
                $query->where('number_bpb', 'LIKE',"%{$search}%");
            })
            ->limit(10)
            ->get();
        return response()->json(['data' => $data]);
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

    public function getDataRetur(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = ProductExpenditure::select('id','warehouse_id','number_bpb as label','date_bpb')->with(
                'warehouse:id,code,name',
                'detail_items',
                'detail_items.product:id,register_number,name,second_name',
                'detail_items.product_status:id,name',
                'detail_items.unit:id,name',
            )
            ->where('is_return_bpb',0)
            ->when($branch, function ($query) use ($branch){
                $query->where('branch_id', $branch);
            })
            ->when($search, function ($query) use ($search){
                $query->where('number_bpb', 'LIKE',"%{$search}%");
            })
            ->limit(10)
            ->get();
        return response()->json(['data' => $data]);
    }
}
