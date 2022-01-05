<?php

namespace App\Models\Inventory;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Master\Unit;
use App\Models\Master\Products;
use App\Models\Master\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExpenditureDetail extends Model
{
    // status 0 => belum di buatkan nomer seri, 1 => sudah dibuatkan nomor seri

    use HasFactory,Observable;
    protected $fillable = [
        'product_expenditure_id',
        'request_item_detail_id',
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

    public function product_expenditure()
    {
        return $this->belongsTo(ProductExpenditure::class, 'product_expenditure_id');
    }

    public function request_item_detail()
    {
        return $this->belongsTo(RequestItemDetail::class, 'request_item_detail_id');
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
