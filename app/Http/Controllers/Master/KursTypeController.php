<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Kurs;
use App\Models\KursType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KursTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-kurs-type', ['except' => ['index']]);
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
        $data = KursType::with(
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
            "code" => "required|unique:kurs,code",
            'name'=>'required|string'
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = KursType::create($request->all());
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
        $data = KursType::find($id);
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
            "code" => "required|unique:kurs,code,".$id,
            'name'=>'required|string'
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = KursType::find($id);
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
            $kurs = KursType::find($id);
            $check = Kurs::where('kurs_type_id',$kurs->id)->first();
            if($check){
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }else{
                $kurs->delete();
                return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
            }
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $data = KursType::select('id','name')->orderBy('name')->get();
        return response()->json(['data' => $data]);
    }
}
