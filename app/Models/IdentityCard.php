<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'name',
        'gender',
        'date_of_birth',
        'blood_type',
        'religion_id',
        'work_type_id',
        'nationality',
        'marital_status_id',
        'address',
        'rt',
        'rw',
        'postal_code_id',
        'village_id',
        'district_id',
        'regency_id',
        'province_id',
        'published_date_ktp',
        'description',
        'insertedBy',
        'updatedBy',
    ];
}
