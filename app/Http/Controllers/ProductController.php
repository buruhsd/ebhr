<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master\Products;

class ProductController extends Controller
{
    public function index(Request $request){
        $search = $request->search;

        return Product::Where('name', 'LIKE',"{$search}%")
                        ->orWhere('product_code', 'LIKE',"{$search}%")->limit(10)->get();
    }

    public function store(){
        $this->validate($request, [
            'name' => 'required',
            'product_code' => 'required'
        ]);
        $data = Product::create($request->all());

        return $data;
    }


}
