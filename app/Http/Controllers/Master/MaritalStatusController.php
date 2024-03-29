<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\MaritalStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaritalStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-marital-status', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
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
            $orderBy = 'status_name';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        $data = MaritalStatus::with(
            'insertedBy:id,name',
            'updatedBy:id,name')
            ->where('status_name','LIKE',"{$search}%")
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
            "code" => "required|unique:marital_statuses,code",
            "status_name" => "required",
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = MaritalStatus::create($request->all());
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
        $data = MaritalStatus::find($id);
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
            "code" => "required|unique:marital_statuses,code,".$id,
            "status_name" => "required",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = MaritalStatus::find($id);
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
        $data = MaritalStatus::find($id)->delete();
        return response()->json(['data' => 'data deleted']);
    }
}
