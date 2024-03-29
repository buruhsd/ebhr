<?php

namespace App\Models\Purchase;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Inventory\ReceiptItems;
use App\Models\Master\Unit;
use App\Models\Master\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'purchase_letter_id',
        'purchase_letter_item_id',
        'purchase_order_id',
        'product_id',
        'unit_id',
        'unit_conversion',
        'qty',
        'rest_qty',
        'price',
        'price_hc',
        'discount',
        'net',
        'net_hc',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function getQtyAttribute()
    {
        return round($this->attributes['qty'],2);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function purchase()
    {
        return $this->belongsTo(PurchaseLetter::class, 'purchase_letter_id');
    }

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseLetterItem::class, 'purchase_letter_item_id');
    }

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function recepit_detail()
    {
        return $this->hasMany(ReceiptItems::class, 'purchase_order_item_id');
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
