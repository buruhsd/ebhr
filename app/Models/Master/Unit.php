<?php

namespace App\Models\Master;

use App\Traits\Observable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'code',
        'name',
        'description',
        'insertedBy',
        'updatedBy',
    ];

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insertedBy');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
}
