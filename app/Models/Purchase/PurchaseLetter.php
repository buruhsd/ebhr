<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLetter extends Model
{
    use HasFactory;
    protected $fillable = [
        'tgl_pp',
        'no_pp',
        'note',
        'branch_id',
        'transaction_type_id',
        'purchase_category_id',
        'purchase_necessary_id',
        'purchase_urgensity_id',
        'insertedBy',
        'updatedBy',
    ];
}
