<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use App\Models\Master\ProductUnit;
use App\Models\Inventory\RequestItem;
use App\Models\Inventory\RequestItemDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestItemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$branch = $request->branch;
    	$status = $request->status;
        $search = $request->search;
        if(is_null($from_date) || is_null($to_date)){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime($to_date. '-10 months'));
        }
        $data = RequestItem::with(
            'branch:id,code,name',
            'organization:id,code,name',
            'bpb_type:id,code,name,is_warehouse,is_number_pkb',
            'usage_group:id,code,name',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
            'approved_by:id,name',
            'closed_by:id,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->when($status, function ($query) use ($status){
                if($status == 'new'){
                    $statusIn = [0];
                }elseif($status == 'approve'){
                    $statusIn = [1];
                }elseif($status == 'reject'){
                    $statusIn = [2];
                }elseif($status == 'close'){
                    $statusIn = [3];
                }elseif($status == 'all'){
                    $statusIn = [0,1,2,3];
                }
                $query->whereIn('status',$statusIn);
            })
            ->when($search, function ($query) use ($search){
                $query->where('number_spb', 'LIKE',"%{$search}%");
            })
            ->when($branch, function ($query) use ($branch){
                $query->whereHas('branch',function ($q) use ($branch){
                    $q->where('branches.id',$branch);
                });
            })
            ->whereDate('date_spb','>=',$from_date)
            ->whereDate('date_spb','<=',$to_date)
            ->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($data);
    }

    public function show($id)
    {
        $data = RequestItem::with(
            'branch:id,code,name',
            'organization:id,code,name,level',
            'bpb_type:id,code,name,is_warehouse,is_number_pkb',
            'usage_group:id,code,name',
            'detail_items',
            'detail_items.product:id,register_number,name,second_name,product_number',
            'detail_items.unit:id,name',
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
            'organization_id' => 'required|exists:organizations,id',
            'bpb_type_id' => 'required|exists:bpb_types,id',
            'usage_group_id' => 'required|exists:usage_groups,id',
            'date_spb' => 'required|date',
            'number_pkb' => 'required|string|max:15',
            'date_pkb' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.qty' => 'required|numeric|min:1'
        ]);

        $request->merge([
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$requestItem = RequestItem::create($request->all());
        foreach($request->item as $value){
            $unit_id = ProductUnit::where(['product_id'=>$value['product_id'],'type'=>'Extern'])->first()->unit_id;
            $value['unit_id'] = $unit_id;
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $requestItem->detail_items()->create($value);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'branch_id' => 'required|exists:branches,id',
            'organization_id' => 'required|exists:organizations,id',
            'bpb_type_id' => 'required|exists:bpb_types,id',
            'usage_group_id' => 'required|exists:usage_groups,id',
            'date_spb' => 'required|date',
            'number_pkb' => 'required|string|max:15',
            'date_pkb' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.qty' => 'required|numeric|min:1'
        ]);

        $requestItem = RequestItem::find($id);
        if(is_null($requestItem)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        $request->merge([
            'updatedBy'=> Auth::id()
        ]);

    	$requestItem->update($request->all());
        $requestItem->detail_items()->delete();
        foreach($request->item as $value){
            $value['insertedBy'] = Auth::id();
            $value['updatedBy'] = Auth::id();
            $requestItem->detail_items()->create($value);
        }
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
    }

    public function destroy($id)
    {
        try {
            $data = RequestItem::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getAutoNumber(Request $request, $branch_id,$type_id)
    {
        $number = RequestItem::generateNumber($branch_id,$type_id);
        return response()->json(['data' => $number]);
    }

    public function getData(Request $request)
    {
        $branch = $request->branch;
        $search = $request->search;
        $data = RequestItem::with(
                'branch:id,code,name',
                'organization:id,code,name,level',
                'bpb_type:id,code,name,is_warehouse,is_number_pkb',
                'usage_group:id,code,name',
                'detail_items',
                'detail_items.product:id,register_number,name,second_name,product_number',
                'detail_items.product.serial_number',
                'detail_items.unit:id,name',
                'insertedBy:id,name',
                'updatedBy:id,name')
            ->where('status',1)
            ->when($branch, function ($query) use ($branch){
                $query->where('branch_id', $branch);
            })
            ->when($search, function ($query) use ($search){
                $query->where('number_spb', 'LIKE',"%{$search}%");
            })
            ->limit(10)
            ->get();
        return response()->json(['data' => $data]);
    }

    public function approve(Request $request,$id)
    {
        $order = RequestItem::find($id);
        $order->status = 1;
        $order->approved_by = Auth::id();
        $order->approved_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil diapprove']);
    }

    public function reject(Request $request,$id)
    {
        $order = RequestItem::find($id);
        $order->status = 2;
        $order->approved_by = Auth::id();
        $order->approved_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil direject']);
    }

    public function close(Request $request,$id)
    {
        $order = RequestItem::find($id);
        $order->status = 3;
        $order->closed_by = Auth::id();
        $order->closed_at = now();
        $order->save();
        return response()->json(['success'=>true, 'message' => 'Data berhasil diclose']);
    }
}
