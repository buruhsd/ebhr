<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdentityCard;
use App\Http\Resources\Hr\IdentityCardResource;
use App\Http\Resources\Hr\IdentityCardResourceCollection;

class HrController extends Controller
{
    public function getHRdata(Request $request){
        $search = $request->search;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        // var_dump($orderby); die();
        $data = IdentityCard::where('id','LIKE',"%{$search}%")
                    ->orWhere('name', 'LIKE',"%{$search}%")
                    ->orderBy($orderBy, $sortBy)
                    ->paginate(10);
        return new IdentityCardResourceCollection($data);
    }
}
