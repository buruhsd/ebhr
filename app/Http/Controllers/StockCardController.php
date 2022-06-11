<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockCard;

class StockCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware('permission:stock-card');
    }

    public function stockCard(Request $request){
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        $data = StockCard::where($orderBy,'LIKE',"{$search}%")
        ->when($orderBy, function ($query) use ($orderBy,$sortBy){
            $query->orderBy($orderBy, $sortBy);
        })
        ->paginate(10);;

        return response()->json($data);        
    }

    public function store(){
        $data = StockCard::create($request->all());

        return response()->json('success', true);
    }
}
