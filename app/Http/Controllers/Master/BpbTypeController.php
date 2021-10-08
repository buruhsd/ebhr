<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\BpbType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BpbTypeController extends Controller
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
        $data = BpbType::where('name','LIKE',"{$search}%")
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
            "code" => "required|unique:bpb_types,code|alpha_num|max:1",
            "name" => "required",
            "is_warehouse" => "required",
            "is_number_pkb" => "required",
        ]);
        $request->merge([
            'code'=>strtoupper($request->code),
            'insertedBy' => Auth::id(),
            'updatedBy'=>Auth::id()
        ]);
        BpbType::create($request->all());
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
        $data = BpbType::find($id);
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
            "code" => "required|alpha_num|max:1|unique:bpb_types,code,".$id,
            "name" => "required",
            "is_warehouse" => "required",
            "is_number_pkb" => "required"
        ]);
        $request->merge(['code'=>strtoupper($request->code),'updatedBy'=>Auth::id()]);
        $data = BpbType::find($id);
        $data->update($request->all());
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
            $data = BpbType::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(){
        $data = BpbType::get();
        return response()->json(['data' => $data]);
    }
}
