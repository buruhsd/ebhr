<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurs extends Model
{
    use HasFactory;
    protected $fillable = [
        'currency_id',
        'kurs_type_id',
        'value',
        'date',
        'insertedBy',
        'updatedBy',
    ];
    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function type()
    {
        return $this->belongsTo(KursType::class, 'kurs_type_id');
    }
}