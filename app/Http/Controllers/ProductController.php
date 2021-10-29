<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master\Products;

class ProductController extends Controller
{
    public function index(Request $request){
        $search = $request->search;
        return Products::select('id','register_number','name','second_name','product_code','unit_id','is_approve')
                    ->with('unit:id,name','serial_number:id,product_id,is_serial_number')
                    ->where('is_approve', 1)
                    ->where(function ($query) use ($search){
                        $query->where('name', 'LIKE',"{$search}%")
                            ->orWhere('register_number', 'LIKE',"{$search}%")
                            ->orWhere('product_code', 'LIKE',"{$search}%");
                    })
                    ->limit(10)->get();
    }

    public function store(){
        $this->validate($request, [
            'name' => 'required',
            'product_code' => 'required'
        ]);
        $data = Products::create($request->all());

        return $data;
    }


}
