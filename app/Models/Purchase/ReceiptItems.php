<?php

namespace App\Models\Purchase;

use App\Models\Master\Unit;
use App\Models\Master\Products;
use App\Models\Master\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptItems extends Model
{
    use HasFactory;
    protected $fillable = [
        'receipt_id',
        'purchase_order_item_id',
        'product_status_id',
        'unit_op_id',
        'unit_id',
        'unit_conversion',
        'qty_op',
        'qty',
        'price_valas',
        'net_valas',
        'kurs',
        'discount',
        'price_idr',
        'net_idr',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function getQtyAttribute()
    {
        return round($this->attributes['qty'],2);
    }

    public function getQtyOpAttribute()
    {
        return round($this->attributes['qty_op'],2);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }

    public function unit_ttb()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function unit_op()
    {
        return $this->belongsTo(Unit::class, 'unit_op_id');
    }

    public function product_status()
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id');
    }

    public function purchase_order_item()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
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
