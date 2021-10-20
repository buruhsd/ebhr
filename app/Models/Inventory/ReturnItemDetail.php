<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Master\Unit;
use App\Models\Master\Products;
use App\Models\Master\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItemDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'return_item_id',
        'product_expenditure_detail_id',
        'product_id',
        'product_status_id',
        'unit_id',
        'qty',
        'note',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function return_item()
    {
        return $this->belongsTo(ReturnItem::class, 'return_item_id');
    }

    public function product_expenditure_detail()
    {
        return $this->belongsTo(ProductExpenditureDetail::class, 'product_expenditure_detail_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function product_status()
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id');
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
