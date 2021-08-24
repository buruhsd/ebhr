<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDescription extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_order_id',
        'item_name',
        'brand',
        'type',
        'noted',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
