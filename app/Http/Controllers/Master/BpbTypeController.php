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
        $this->middleware('permission:master-bpb');
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
        $data = BpbType::with(
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
            "code" => "required|unique:bpb_types,code|alpha_num|max:1",
            "alias_code" => "required|unique:bpb_types,alias_code|alpha_num",
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
            "alias_code" => "nullable|alpha_num|unique:bpb_types,alias_code,".$id,
            "name" => "required",
            "is_warehouse" => "required",
            "is_number_pkb" => "required"
        ]);
        $request->merge(['code'=>strtoupper($request->code),'updatedBy'=>Auth::id()]);
        $data = BpbType::find($id);
        if(!$data->alias_code){
            $request->merge(['alias_code'=>strtoupper($request->alias_code)]);
        }
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

    public function customType()
    {
        $items = [];
        $types = BpbType::select('name')->get();
        foreach($types as $value){
            $item = [
                'id'=>'BPB - '.$value->name,
                'label'=>'BPB - '.$value->name
            ];
            array_push($items,$item);
        }
        $data = array(
            ['id'=>'TTB','label'=>'TTB'],
            ['id'=>'PBP','label'=>'PBP'],
            ['id'=>'Memorial','label'=>'Memorial']
        );
        $data = array_merge($items,$data);
        return response()->json($data);
    }
}
