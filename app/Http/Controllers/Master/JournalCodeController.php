<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\JournalCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JournalCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-journal-code', ['except' => ['index']]);
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
            $orderBy = 'code';
        }
        if(is_null($sortBy)){
            $sortBy = 'desc';
        }

        $data = JournalCode::with('insertedBy:id,name','updatedBy:id,name')
                ->when($search, function ($query) use ($search,$orderBy){
                    $query->where($orderBy,$search);
                })
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
            'code'=>'required|unique:journal_codes,code',
            'name'=>'required|string',
            'type'=>'required|unique:journal_codes,type',
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = JournalCode::create($request->all());
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
        $data = JournalCode::with('insertedBy:id,name','updatedBy:id,name')->find($id);
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
            'code'=>'required|unique:journal_codes,code,'.$id,
            'name'=>'required|string',
            'type'=>'required|unique:journal_codes,type,'.$id,
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = JournalCode::find($id);
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
            $data = JournalCode::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }
}
