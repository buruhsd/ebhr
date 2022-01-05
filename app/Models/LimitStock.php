<?php

namespace App\Models;

use App\Traits\Observable;
use App\Models\Master\Warehouse;
use App\Models\Master\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LimitStock extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'min',
        'max',
        'expired_at',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
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
