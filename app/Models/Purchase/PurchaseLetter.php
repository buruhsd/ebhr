<?php

namespace App\Models\Purchase;

use App\Models\Branch;
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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function purchase_category()
    {
        return $this->belongsTo(PurchaseCategory::class, 'purchase_category_id');
    }

    public function purchase_necessary()
    {
        return $this->belongsTo(PurchaseNecessary::class, 'purchase_necessary_id');
    }

    public function purchase_urgentity()
    {
        return $this->belongsTo(PurchaseUrgentity::class, 'purchase_urgensity_id');
    }
}
