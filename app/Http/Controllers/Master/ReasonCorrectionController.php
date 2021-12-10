<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\ReasonCorrection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReasonCorrectionController extends Controller
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
        $data = ReasonCorrection::with(
                'chart_of_account:id,name',
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
            "code" => "required|unique:reason_corrections,code|alpha_num|max:10",
            "name" => "required|string|max:20",
            "dk" => "required|in:D,d,K,k",
            "chart_of_account_id" => "required|exists:chart_of_accounts,id",
        ]);
        $request->merge([
            'code'=>strtoupper($request->code),
            'dk'=>strtoupper($request->dk),
            'insertedBy' => Auth::id(),
            'updatedBy'=>Auth::id()
        ]);
        ReasonCorrection::create($request->all());
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
        $data = ReasonCorrection::find($id);
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
            "code" => "required|alpha_num|max:10|unique:reason_corrections,code,".$id,
            "name" => "required|string|max:20",
            "dk" => "required|in:D,d,K,k",
            "chart_of_account_id" => "required|exists:chart_of_accounts,id",
        ]);
        $request->merge([
            'code'=>strtoupper($request->code),
            'dk'=>strtoupper($request->dk),
            'updatedBy'=>Auth::id()
        ]);
        ReasonCorrection::find($id)->update($request->all());
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
            $data = ReasonCorrection::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(){
        $data = ReasonCorrection::with('chart_of_account')->get();
        return response()->json(['data' => $data]);
    }
}
