<?php

namespace App\Models\Purchase;

use App\Traits\Observable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDescription extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'purchase_order_id',
        'noted',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
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
