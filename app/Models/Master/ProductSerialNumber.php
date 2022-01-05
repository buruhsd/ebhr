<?php

namespace App\Models\Master;

use App\Traits\Observable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerialNumber extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'product_id',
        'is_return',
        'is_serial_number',
        'status',
        'insertedBy',
        'updatedBy',
    ];
    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
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
