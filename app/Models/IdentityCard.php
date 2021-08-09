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

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->nik.' - '.$this->name;
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function work_type()
    {
        return $this->belongsTo(WorkStatus::class, 'work_type_id');
    }

    public function marital_status()
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id');
    }

    public function postal_code()
    {
        return $this->belongsTo(PostalCode::class, 'postal_code_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
