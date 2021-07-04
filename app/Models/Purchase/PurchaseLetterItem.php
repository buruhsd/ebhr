<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLetterItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'qty',
        'unit',
        'insertedBy',
        'updatedBy',
    ];
}
