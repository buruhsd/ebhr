<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\WorkType;
use App\Models\DevelopmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'tgl_surat',
        'no_surat',
        'no_induk',
        'name_alias',
        'identity_id',
        'work_pattern_id',
        'work_group_id',
        'work_type_id',
        'position_id',
        'employee_status_id',
        'development_status_id',
        'start_date',
        'description',
        'insertedBy',
        'updatedBy',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->no_induk = self::numberInduk($model->branch_id,$model->work_type_id);
                $model->no_surat = self::numberSurat($model->development_status_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function numberInduk($branchId,$typeId)
    {
        $branch = Branch::find($branchId)->code;
        $type = WorkType::find($typeId)->code;
        $string = $branch.$type.'0000';
        $latest = self::orderBy('id','desc')->first();
        if($latest){
            $string = $latest->no_induk;
        }
        $id = substr($string, -4);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 4, '0', STR_PAD_LEFT);
        return $branch.$type.$newID;
    }

    public static function numberSurat($id)
    {
        $code = DevelopmentStatus::find($id)->code;
        $string = $code.date('y').'/'.date('m').'/0000';
        $latest = self::orderBy('id','desc')->first();
        if($latest){
            $string = $latest->no_surat;
        }
        $id = substr($string, -4);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 4, '0', STR_PAD_LEFT);
        return $code.date('y').'/'.date('m').'/'.$newID;
    }

    public function identity()
    {
        return $this->belongsTo(IdentityCard::class, 'identity_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function work_pattern()
    {
        return $this->belongsTo(WorkPattern::class, 'work_pattern_id');
    }

    public function work_group()
    {
        return $this->belongsTo(WorkGroup::class, 'work_group_id');
    }

    public function work_type()
    {
        return $this->belongsTo(WorkType::class, 'work_type_id');
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
