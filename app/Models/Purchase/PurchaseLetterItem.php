<?php

namespace App\Models\Purchase;

use App\Models\Master\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLetterItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'qty',
        'unit',
        'status',
        'insertedBy',
        'updatedBy',
    ];
    protected $appends = ['label'];
    public function getLabelAttribute()
    {
        return $this->products ? $this->products->register_number.' '.$this->products->second_name : '';
    }

    public function products()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_letter_item_id');
    }
}
