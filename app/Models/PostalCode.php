<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'postal_code',
        'village_name',
        'district_name',
        'regency_name',
        'province_name'
    ];

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->postal_code;
    }
}
