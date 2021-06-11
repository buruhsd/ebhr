<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'tgl_surat',
        'no_surat',
        'no_induk',
        'name_alias',
        'identity_id',
        'work_pattern_id',
        'work_group_id',
        'position_id',
        'employee_status_id',
        'development_status_id',
        'start_date',
        'description',
        'insertedBy',
        'updatedBy',
    ];


    public function identity()
    {
        return $this->belongsTo(IdentityCard::class, 'identity_id');
    }

    public function work_pattern()
    {
        return $this->belongsTo(WorkPattern::class, 'work_pattern_id');
    }

    public function work_group()
    {
        return $this->belongsTo(WorkGroup::class, 'work_group_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function employee_status()
    {
        return $this->belongsTo(EmployeeStatus::class, 'employee_status_id');
    }

    public function development_status()
    {
        return $this->belongsTo(DevelopmentStatus::class, 'development_status_id');
    }
}
