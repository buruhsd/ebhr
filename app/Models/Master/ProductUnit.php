<?php

namespace App\Models\Master;

use App\Traits\Observable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'product_id',
        'unit_id',
        'name',
        'type',
        'value',
        'insertedBy',
        'updatedBy',
    ];

    protected $appends = ['label'];
    public function getLabelAttribute()
    {
        return $this->unit->name;
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
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
