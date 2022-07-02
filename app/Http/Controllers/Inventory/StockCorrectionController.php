<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\ReasonCorrection;
use App\Models\Inventory\StockCorrection;
use App\Models\Inventory\StockCorrectionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockCorrectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:inventory', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $data = StockCorrection::with(
                'branch:id,code,name',
                'warehouse:id,code,name',
                'reason_correction:id,code,name',
                'detail_items',
                'detail_items.product:id,register_number,name,second_name,product_number',
                'detail_items.unit:id,name',
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
        $data = StockCorrection::with(
            'branch:id,code,name',
            'warehouse:id,code,name',
            'reason_correction:id,code,name',
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
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason_correction_id' => 'required|exists:reason_corrections,id',
            'date' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.unit_id' => 'required|distinct|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1',
            'item.*.price' => 'nullable|numeric',
            'item.*.status' => 'required|string'
        ]);

        $reason = ReasonCorrection::find($request->reason_correction_id);
        $request->merge([
            'chart_of_account_id' => $reason->chart_of_account_id,
            'opponent_estimate' => $reason->chart_of_account->name,
            'correction_type' => $reason->dk,
            'insertedBy' => Auth::id(),
            'updatedBy'=> Auth::id()
        ]);

    	$saveData = StockCorrection::create($request->all());
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
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason_correction_id' => 'required|exists:reason_corrections,id',
            'date' => 'required|date',
            'note' => 'required|string',
            'item' => 'required',
            'item.*.product_id' => 'required|distinct|exists:products,id',
            'item.*.unit_id' => 'required|distinct|exists:units,id',
            'item.*.qty' => 'required|numeric|min:1',
            'item.*.price' => 'nullable|numeric',
            'item.*.status' => 'required|string'
        ]);

        $updateData = StockCorrection::find($id);
        if(is_null($updateData)){
            return response()->json(['success' => false, 'message' => 'Data tidak ada']);
        }

        $reason = ReasonCorrection::find($request->reason_correction_id);
        $request->merge([
            'chart_of_account_id' => $reason->chart_of_account_id,
            'opponent_estimate' => $reason->chart_of_account->name,
            'correction_type' => $reason->dk,
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
            $data = StockCorrection::find($id)->delete();
            return response()->json(['success'=>true, 'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getAutoNumber(Request $request, $branch_id)
    {
        $number = StockCorrection::generateNumber($branch_id);
        return response()->json(['data' => $number]);
    }
}
