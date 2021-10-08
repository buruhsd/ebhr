<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Branch;
use App\Models\Purchase\PurchaseLetter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
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
            $orderBy = 'name';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = Branch::with(
                'insertedBy:id,name',
                'updatedBy:id,name')
                ->where('name','LIKE',"{$search}%")
                ->orderBy($orderBy, $sortBy)
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
            "code" => "required",
            "name" => "required",
            "alias_name" => "required|unique:branches,alias_name|alpha|string|max:2"
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id(),'alias_name'=>strtoupper($request->alias_name)]);
        $data = Branch::create($request->all());
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
        $data = Branch::find($id);
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
            "code" => "required",
            "name" => "required",
            "alias_name" => "required|alpha|string|max:2|unique:branches,alias_name,".$id
        ]);
        $request->merge(['updatedBy'=>Auth::id(),'alias_name'=>strtoupper($request->alias_name)]);
        $data = Branch::find($id);
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
        $check = PurchaseLetter::where('branch_id',$id)->first();
        if($check){
            return response()->json(['message' => 'Data sudah digunakan pada Tabel/Transaksi lain','success'=>false]);
        }else{
            $data = Branch::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }
    }
}
