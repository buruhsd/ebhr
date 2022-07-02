<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\SupplierStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-supplier-status', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    }

    /**
     * Display the all resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        if(is_null($orderBy)){
            $orderBy = 'name';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = SupplierStatus::with('supplier.partner.npwp:id,number_npwp,name',
                    'supplier.category:id,name',
                    'supplier.currency:id,name',
                    'supplier.partner.postal_code:id,postal_code',
                    'supplier.partner.village:id,name',
                    'supplier.partner.district:id,name',
                    'supplier.partner.regency:id,name',
                    'supplier.partner.province:id,name',
                    'product_status:id,code,name',
                    'insertedBy:id,name',
                    'updatedBy:id,name')
                ->whereHas('supplier.partner',function ($query) use ($search,$sortBy,$orderBy){
                    $query->where('partners.name','LIKE',"%{$search}%")
                    ->orderBy('partners.'.$orderBy, $sortBy);
                })
                ->paginate(10);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required|exists:suppliers,id',
            'product_status_id' => 'required|exists:product_statuses,id'
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = SupplierStatus::create($request->all());
        return response()->json(['data'=>$data]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = SupplierStatus::with('supplier.partner.npwp:id,number_npwp,name',
            'supplier.partner.postal_code:id,postal_code',
            'supplier.partner.village:id,name',
            'supplier.partner.district:id,name',
            'supplier.partner.regency:id,name',
            'supplier.partner.province:id,name',
            'product_status:id,code,name')->find($id);
        return response()->json(['data'=>$data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'supplier_id' => 'required|exists:suppliers,id',
            'product_status_id' => 'required|exists:product_statuses,id'
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = SupplierStatus::find($id);
        $data->update($request->all());
        return response()->json(['data'=>$data]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = SupplierStatus::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }
}
