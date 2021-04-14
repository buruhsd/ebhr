<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdentityCard;

class HrController extends Controller
{
    public function getHRdata(Request $request){
    	$data = IdentityCard::paginate(10);

    	return response()->json($data);
    }
}
