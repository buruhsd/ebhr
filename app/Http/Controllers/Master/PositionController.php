<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Position;
use App\Exports\PositionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-position', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
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
        $data = Position::with(
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
            "code_position" => "required|unique:positions,code_position",
            'name' => 'required',
            'code_shorting' => 'required|unique:positions,code_shorting',
            'is_struktural' => 'required'
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Position::create($request->all());
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
        $data = Position::find($id);
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
            "code_position" => "required|unique:positions,code_position,".$id,
            'name' => 'required',
            'code_shorting' => 'required|unique:positions,code_shorting,'.$id,
            'is_struktural' => 'required'
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Position::find($id);
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
        $data = Position::find($id)->delete();
        return response()->json(['data' => 'data deleted']);
    }

    public function export_excel()
	{
		return Excel::download(new PositionExport, 'position_'.time().'.xlsx');
	}
}
