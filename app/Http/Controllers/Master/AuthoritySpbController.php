<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\AuthoritySpb;
use App\Models\Master\Products;
use App\Models\OrganizationLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthoritySpbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
            $orderBy = 'id';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = AuthoritySpb::with(
                'product:id,register_number,name,second_name',
                'level:id,name',
                'insertedBy:id,name',
                'updatedBy:id,name')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('product', function ($product) use ($search){
                        $product->where('products.register_number',$search)
                            ->orWhere('products.name',$search)
                            ->orWhere('products.second_name',$search);
                    })
                    ->orWhereHas('level', function ($level) use ($search){
                        $level->where('organization_levels.name',$search);
                    });
                })
                ->when($orderBy, function ($query) use ($orderBy,$sortBy){
                    if($orderBy == 'register_number' || $orderBy == 'name'){
                        $query->orderBy(Products::select($orderBy)
                            ->whereColumn('products.id', 'authority_spbs.product_id')
                        ,$sortBy);
                    }elseif($orderBy == 'organization'){
                        $query->orderBy(OrganizationLevel::select('name')
                            ->whereColumn('organization_levels.id', 'authority_spbs.approval_level_id')
                        ,$sortBy);
                    }else{
                        $query->orderBy($orderBy, $sortBy);
                    }
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
            'product_id' => 'required|exists:products,id|unique:authority_spbs,product_id,'.$request->product_id.',id,approval_level_id,'.$request->approval_level_id,
            'approval_level_id' => 'required|exists:organization_levels,id|unique:authority_spbs,approval_level_id,'.$request->approval_level_id.',id,product_id,'.$request->product_id,
        ]);
        $request->merge([
            'insertedBy' => Auth::id(),
            'updatedBy'=>Auth::id()
        ]);
        AuthoritySpb::create($request->all());
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = AuthoritySpb::with(
            'product:id,register_number,name,second_name',
            'level:id,name',
            'insertedBy:id,name',
            'updatedBy:id,name')->find($id);
        return response()->json(['success' => true, 'data'=>$data]);
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
            'product_id' => 'required|exists:products,id|unique:authority_spbs,product_id,'.$id.',id,approval_level_id,'.$request->approval_level_id,
            'approval_level_id' => 'required|exists:organization_levels,id|unique:authority_spbs,approval_level_id,'.$id.',id,product_id,'.$request->product_id,
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        AuthoritySpb::find($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'Data berhasil diperbaharui']);
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
            $data = AuthoritySpb::find($id)->delete();
            return response()->json(['success'=>true,'message' => 'Data berhasil dihapus']);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['success'=>false,'message' => 'Data tidak boleh dihapus']);
            }
        }
    }
}
