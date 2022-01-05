<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    use HasFactory,Observable;
    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }
}
