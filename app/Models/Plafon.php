<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plafon extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'max_price_unit',
        'max_amount_item',
        'max_amount_op',
        'used_at',
        'approval_level_id',
        'release_level_id',
        'insertedBy',
        'updatedBy',
    ];

    public function approval_level()
    {
        return $this->belongsTo(OrganizationLevel::class, 'approval_level_id');
    }

    public function release_level()
    {
        return $this->belongsTo(OrganizationLevel::class, 'release_level_id');
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
