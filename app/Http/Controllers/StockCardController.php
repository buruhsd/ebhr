<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockCard;

class StockCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:stock-card');
    }

    public function stockCard(){
        $data = StockCard::paginate(10);

        return response()->json($data);
    }

    public function store(){
        $data = StockCard::create($request->all());

        return response()->json('success', true);
    }
}
