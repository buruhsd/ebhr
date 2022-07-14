<?php

namespace App\Models;

use App\Traits\Observable;
use App\Models\Master\Products;
use App\Models\Master\Warehouse;
use App\Models\Master\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    use HasFactory, Observable;

    protected $fillable = [
        'branch_id',
        'warehouse_id',
        'product_id',
        'product_status_id',
        'date_year',
        'qty_awal',
        'hpp_awal',
        'nil_awal',
        'qty_debit',
        'nil_debit',
        'qty_credit',
        'nil_credit',
        'qty_akhir',
        'hpp_akhir',
        'nil_akhir',
        'qty_temp',
        'post_date'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function product_status()
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id');
    }
}
