<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;

use App\Models\IdentityCard;
use App\Models\Employee;

class EmployeeController extends Controller
{
	public function IdentityCardList(){
		$page_title = 'Hr System';
        $page_description = 'DATA KTP';

		return view('hr.employee', compact('page_title', 'page_description'));

	}
    public function getIdentityCard(Request $request){

    	if ($request->ajax()) {
            $data = IdentityCard::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                           $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';

                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function getpaginate(Request $request){
        $data = IdentityCard::paginate(2);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function EmployeeList(){
        $page_title = 'Hr System';
        $page_description = 'DATA KARYAWAN';

        return view('hr.employee', compact('page_title', 'page_description'));

    }
    public function getEmployee(Request $request){

        if ($request->ajax()) {
            $data = IdentityCard::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                           $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';

                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function apiEmployee(Request $request)
    {
        $data = IdentityCard::orderBy('name','asc')->paginate(100);
        return $data;
    }
}
