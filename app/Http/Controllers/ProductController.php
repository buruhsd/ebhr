<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master\Product;

class ProductController extends Controller
{
    public function index(Request $request){
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        if(is_null($orderBy)){
            $orderBy = 'name';
        }
        if(is_null($sortBy)){
            $sortBy = 'asc';
        }
        return Product::Where('name', 'LIKE',"{$search}%")
            ->orderBy($orderBy, $sortBy)->paginate(20);
    }


}
