<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockBalance;

class StockBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware('permission:stock-balance');
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        $data = StockBalance::with(
            'branch:id,name,code,alias_name',
            'warehouse:id,name,code',
            'product:id,name,registe_number',
            'product_status:id,name,code',
        )
        ->when($orderBy, function ($query) use ($orderBy,$sortBy,$search){
            $query->whereHas($orderBy, function($q) use ($orderBy,$search){
                $field = 'branches.name';
                if($orderBy == 'product'){
                    $field = 'products.name';
                }elseif($orderBy == 'warehouse'){
                    $field = 'warehouses.name';
                }
                $q->where($field,'LIKE',"{$search}%");
            })->orderBy($orderBy, $sortBy);
        })
        ->paginate(20);;
        return response()->json($data);        
    }
}
