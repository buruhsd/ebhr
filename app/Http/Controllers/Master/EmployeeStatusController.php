<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\EmployeeStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
            "code_shorting" => "required",
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = EmployeeStatus::create($request->all());
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
        $data = EmployeeStatus::find($id);
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
            "code_shorting" => "required",
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = EmployeeStatus::find($id);
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
        $data = EmployeeStatus::find($id)->delete();
        return response()->json(['data' => 'data deleted']);
    }
}
