<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkGroup extends Model
{
    use HasFactory;
    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }
}
