<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasFactory;
    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->postal_code;
    }
}
