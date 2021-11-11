<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'type',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insertedBy');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
}
