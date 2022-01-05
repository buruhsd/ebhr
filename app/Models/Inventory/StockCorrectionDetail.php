<?php

namespace App\Models\Inventory;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Master\Unit;
use App\Models\Master\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCorrectionDetail extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'stock_correction_id',
        'product_id',
        'unit_id',
        'qty',
        'price',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function stock_correction()
    {
        return $this->belongsTo(StockCorrection::class, 'stock_correction_id');
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
