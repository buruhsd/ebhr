<?php

namespace App\Models\Master;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'description',
        'branch_id',
        'insertedBy',
        'updatedBy',
    ];
    
    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
