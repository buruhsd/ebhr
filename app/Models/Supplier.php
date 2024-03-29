<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'partner_id',
        'supplier_category_id',
        'currency_id',
        'term_of_payment',
        'is_tt',
        'insertedBy',
        'updatedBy',
    ];

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->partner->code.' '.$this->partner->name;
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function category()
    {
        return $this->belongsTo(SupplierCategory::class, 'supplier_category_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function product_status()
    {
        return $this->hasOne(SupplierStatus::class, 'supplier_id');
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
