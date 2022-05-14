<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index(){
        $menu = Menu::where('parent_id', 0)->get();

        return response()->json(compact('menu'));
    }

    public function store(Request $request){

        return Menu::create($request->all());
    }

    public function update(Request $request, $id){
        $menu = Menu::findOrFail($id);
        return $menu->update($request->all());
    }

    public function delete($id){
        return Menu::find($id)->delete();
    }

}
