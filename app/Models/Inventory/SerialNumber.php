<?php

namespace App\Models\Inventory;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Branch;
use App\Models\Master\Products;
use App\Models\Master\Warehouse;
use App\Models\Master\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'branch_id',
        'warehouse_id',
        'receipt_id',
        'receipt_item_id',
        'product_expenditure_id',
        'product_expenditure_detail_id',
        'product_id',
        'product_status_id',
        'type',
        'dk',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function details()
    {
        return $this->hasMany(SerialNumberDetail::class, 'serial_number_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }

    public function receipt_item()
    {
        return $this->belongsTo(ReceiptItems::class, 'receipt_item_id');
    }

    public function product_expenditure()
    {
        return $this->belongsTo(ProductExpenditure::class, 'product_expenditure_id');
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

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insertedBy');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
}
