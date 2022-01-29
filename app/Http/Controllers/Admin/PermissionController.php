<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $permission  = Permission::where('name', 'LIKE',"%{$search}%")
                    ->orWhere('guard_name', 'LIKE',"%{$search}%")
                    ->orderBy('name', 'asc')
                    ->paginate(10);
        return response()->json($permission);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'guard_name' => 'required',
        ]);
        $data = Permission::create($request->all());

        return response()->json($data);
    }

    public function show($id)
    {
        $data = Permission::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'guard_name' => 'required',
        ]);

        $data = Permission::find($id)->update($request->all());
        return response()->json($data);
    }

    public function delete(Request $request, $id)
    {
        $data = Permission::find($id)->update($request->all());
        return response()->json(['success' => false, 'message' => 'Data dihapus']);
    }

}
