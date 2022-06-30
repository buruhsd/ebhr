<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Religion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReligionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-religion', ['except' => ['index']]);
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
            $orderBy = 'religion_name';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = Religion::with(
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->where('religion_name','LIKE',"{$search}%")
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
            "code" => "required|unique:religions,code",
            "religion_name" => "required",
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Religion::create($request->all());
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
        $data = Religion::find($id);
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
            "code" => "required|unique:religions,code,".$id,
            "religion_name" => "required",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Religion::find($id);
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
        $data = Religion::find($id)->delete();
        return response()->json(['data' => 'data deleted']);
    }
}
