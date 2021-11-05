<?php

namespace App\Models;

use App\Models\Master\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthoritySpb extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'approval_level_id',
        'insertedBy',
        'updatedBy',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function level()
    {
        return $this->belongsTo(OrganizationLevel::class, 'approval_level_id');
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
