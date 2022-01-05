<?php

namespace App\Models\Inventory;

use App\Traits\Observable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerialNumberDetail extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'serial_number_id',
        'no_seri',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    public function serial_number()
    {
        return $this->belongsTo(SerialNumber::class, 'serial_number_id');
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
