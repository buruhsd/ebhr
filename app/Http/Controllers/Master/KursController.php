<?php

namespace App\Http\Controllers\Master;

use Auth;
use App\Models\Kurs;
use App\Models\Currency;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KursController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:master-kurs');
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
            $orderBy = 'date';
        }
        if(is_null($sortBy)){
            $sortBy = 'desc';
        }
        
        $data = Kurs::with('currency:id,name,code','type:id,name','insertedBy:id,name',
            'updatedBy:id,name')
            ->when($search, function ($query) use ($search){
                $query->whereHas('type', function ($q) use ($search){
                    $q->where('kurs_types.name',$search);
                });
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
            'currency_id'=>'required|integer',
            'kurs_type_id'=>'required|integer',
            'value'=>'required|numeric',
            'date'=>'required|date',
            'number_kmk' => 'required|string|max:25',
            'kmk_at' => 'required|date',
        ]);
        $request->merge(['insertedBy' => Auth::id(),'updatedBy'=>Auth::id()]);
        $data = Kurs::create($request->all());
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
        $data = Kurs::find($id);
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
            'currency_id'=>'required|integer',
            'kurs_type_id'=>'required|integer',
            'value'=>'required|numeric',
            'date'=>'required|date',
            'number_kmk' => 'required|string|max:25',
            'kmk_at' => 'required|date',
        ]);
        $request->merge(['updatedBy'=>Auth::id()]);
        $data = Kurs::find($id);
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
            $data = Kurs::find($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus','success'=>true]);
        }catch(\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return response()->json(['message' => 'Data tidak boleh dihapus','success'=>false]);
            }
        }
    }

    public function getData(Request $request)
    {
        $data = Kurs::get();
        return response()->json(['data' => $data]);
    }

    public function getKurs(Request $request)
    {
        $data = Kurs::select('value')
                ->where([
                    'currency_id'=>$request->currency_id,
                    'kurs_type_id'=>$request->kurs_type_id
                ])
                ->whereDate('date','<=',now())
                ->orderBy('date','desc')
                ->first();
        return response()->json(['data' => $data]);
    }
}
