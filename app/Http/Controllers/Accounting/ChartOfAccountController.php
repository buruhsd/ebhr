<?php

namespace App\Http\Controllers\Accounting;

use Auth;
use App\Models\Accounting\ChartOfAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
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
            $orderBy = 'code';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = ChartOfAccount::with(
                'valas:id,name',
                'insertedBy:id,name',
                'updatedBy:id,name')
                ->where($orderBy,'LIKE',"{$search}%")
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
            "parent_id" => "nullable|integer",
            "code" => "required|integer|unique:chart_of_accounts,code",
            "name" => "required|string",
            "level" => "required|integer",
            "normal_balance" => "required|max:1",
            "detail_general" => "required|max:1",
            "classification" => "required",
            "currency_id" => "nullable|exists:currencies,id",
            "is_close" => "required",
        ]);

        $level = $request->level;
        $classification = $request->classification;
        if($request->parent_id){
            $parent = ChartOfAccount::find($request->parent_id);
            $level = $parent->level;
            $classification = $parent->classification;
        }

        $request->merge([
            'level'=> $level,
            'classification'=> $classification,
            'insertedBy' => Auth::id(),
            'updatedBy'=>Auth::id()
        ]);
        ChartOfAccount::create($request->all());
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
        $data = ChartOfAccount::with(
            'valas:id,name',
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
            "parent_id" => "nullable|integer",
            "code" => "required|integer|unique:chart_of_accounts,code,".$id,
            "name" => "required|string",
            "level" => "required|integer",
            "normal_balance" => "required|max:1",
            "detail_general" => "required|max:1",
            "classification" => "required",
            "currency_id" => "nullable|exists:currencies,id",
            "is_close" => "required",
        ]);

        $updateData = ChartOfAccount::find($id);
        $level = $request->level;
        $classification = $request->classification;
        if($request->parent_id){
            $parent = ChartOfAccount::find($request->parent_id);
            $level = $parent->level;
            $classification = $parent->classification;
        }

        $request->merge([
            'level'=> $level,
            'classification'=> $classification,
            'insertedBy' => Auth::id(),
            'updatedBy'=>Auth::id()
        ]);
        $updateData->update($request->all());
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
            $data = ChartOfAccount::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData()
    {
        $data = ChartOfAccount::where('detail_general','U')->get();
        return response()->json(['data' => $data]);
    }
}
