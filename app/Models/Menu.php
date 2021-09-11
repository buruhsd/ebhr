<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Menu extends Model
{
    use HasFactory;

    public function permission(){
        return $this->hasOne(Permission::class, 'permission_id');
    }
}
