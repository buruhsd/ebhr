<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Master\Unit;
use App\Models\Master\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestItemDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_item_id',
        'product_id',
        'unit_id',
        'qty',
        'rest_qty',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->rest_qty = $model->qty;
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public function request_item_id()
    {
        return $this->belongsTo(RequestItem::class, 'request_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
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
