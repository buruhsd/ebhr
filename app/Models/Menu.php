<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Menu extends Model
{
    use HasFactory,Observable;

    protected $appends = ['childs'];

    public function getChildsAttribute(){
        return $this->child()->get();
    }

    public function permission(){
        return $this->hasOne(Permission::class, 'permission_id');
    }

    public function child(){
        return $this->hasMany(Menu::class, 'parent_id');
    }
}
