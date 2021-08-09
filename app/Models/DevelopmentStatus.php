<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'abbreviation',
        'description',
        'insertedBy',
        'updatedBy',
    ];
    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }
}
