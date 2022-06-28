<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-supplier');
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
        $data = Customer::with('partner.npwp:id,number_npwp,name','partner.postal_code:id,postal_code','partner.village:id,name','partner.district:id,name','partner.regency:id,name','partner.province:id,name',
                'currency:id,name','group:id,name',
                'insertedBy:id,name',
                'updatedBy:id,name')
                ->whereHas('partner',function ($query) use ($search,$sortBy,$orderBy){
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
            'partner_id' => 'required|integer',
            'customer_group_id' => 'required|integer',
            'currency_id' => 'nullable|integer',
            'term_of_payment' => 'required|integer',
            'is_tt'=> 'required'
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Customer::create($request->all());
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
        $data = Customer::find($id);
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
            'partner_id' => 'required|integer',
            'customer_group_id' => 'required|integer',
            'currency_id' => 'nullable|integer',
            'term_of_payment' => 'required|integer',
            'is_tt'=> 'required'
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        if(is_null($request->currency_id)){
            $request->merge(['currency_id'=>NULL]);
        }
        $data = Customer::find($id);
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
            $data = Customer::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $search = $request->q;
        $data = Customer::with('partner.npwp:id,number_npwp,name','partner.postal_code:id,postal_code','partner.village:id,name','partner.district:id,name','partner.regency:id,name','partner.province:id,name',
        'currency:id,name,code','group:id,name')
                ->whereHas('partner',function ($query) use ($search){
                    $query->where('partners.code','LIKE',"%{$search}%")
                    ->orWhere('partners.name','LIKE',"%{$search}%");
                })
                ->limit(10)
                ->get();
        return response()->json(['data' => $data]);
    }

    public function getDataNotStatus(Request $request)
    {
        $search = $request->q;
        $data = Customer::with('partner.npwp:id,number_npwp,name','partner.postal_code:id,postal_code','partner.village:id,name','partner.district:id,name','partner.regency:id,name','partner.province:id,name',
                'currency:id,name,code','group:id,name')
                ->whereHas('partner',function ($query) use ($search){
                    $query->where('partners.code','LIKE',"%{$search}%")
                    ->orWhere('partners.name','LIKE',"%{$search}%");
                })
                ->limit(10)
                ->get();
        return response()->json(['data' => $data]);
    }
}
