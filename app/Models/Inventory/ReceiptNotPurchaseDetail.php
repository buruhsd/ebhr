<?php

namespace App\Models\Inventory;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Master\Unit;
use App\Models\Master\Products;
use App\Models\Master\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptNotPurchaseDetail extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'receipt_not_purchase_id',
        'expenditure_detail_id',
        'product_id',
        'product_status_id',
        'unit_id',
        'qty',
        'is_serial_number',
        'is_return',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function receipt_not_purchase()
    {
        return $this->belongsTo(ReceiptNotPurchase::class, 'receipt_not_purchase_id');
    }

    public function expenditure_detail()
    {
        return $this->belongsTo(ProductExpenditureDetail::class, 'expenditure_detail_id');
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
