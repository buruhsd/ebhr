<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Plafon;
use App\Models\Position;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlafonController extends Controller
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
            $orderBy = 'used_at';
        }
        if(is_null($sortBy)){
            $sortBy = 'desc';
        }
        $data = Plafon::with('approval_level:id,code_position,name','release_level:id,code_position,name',
            'insertedBy:id,name',
            'updatedBy:id,name')
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
            'max_price_unit' => 'required|numeric',
            'max_amount_item' => 'required|numeric',
            'max_amount_op' => 'required|numeric',
            'used_at' => 'required|date',
            'approval_level_id' => 'required|integer|exists:positions,id',
            'release_level_id' => 'required|integer|exists:positions,id'
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Plafon::create($request->all());
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
        $data = Plafon::find($id);
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
            'max_price_unit' => 'required|numeric',
            'max_amount_item' => 'required|numeric',
            'max_amount_op' => 'required|numeric',
            'used_at' => 'required|date',
            'approval_level_id' => 'required|integer|exists:positions,id',
            'release_level_id' => 'required|integer|exists:positions,id'
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Plafon::find($id);
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
        $data = Plafon::find($id)->delete();
        return response()->json(['data' => 'data deleted']);
    }
}
