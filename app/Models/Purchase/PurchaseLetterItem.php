<?php

namespace App\Models\Purchase;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Master\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLetterItem extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'purchase_letter_id',
        'product_id',
        'qty',
        'rest_qty',
        'unit',
        'status',
        'insertedBy',
        'updatedBy',
    ];
    protected $appends = ['label'];

    public function getQtyAttribute()
    {
        return round($this->attributes['qty'],2);
    }

    public function getRestQtyAttribute()
    {
        return round($this->attributes['rest_qty'],2);
    }

    public function getLabelAttribute()
    {
        // return $this->products ? $this->products->register_number.' '.$this->products->second_name : '';
        return $this->products ? $this->products->second_name : '';
    }

    public function purchase()
    {
        return $this->belongsTo(PurchaseLetter::class, 'purchase_letter_id');
    }

    public function products()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_letter_item_id');
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
