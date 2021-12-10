<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Branch;
use App\Models\Master\Warehouse;
use App\Models\ReasonCorrection;
use App\Models\Accounting\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCorrection extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'warehouse_id',
        'number',
        'date',
        'reason_correction_id',
        'chart_of_account_id',
        'opponent_estimate',
        'correction_type',
        'note',
        'status',
        'insertedBy',
        'updatedBy',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->number = self::generateNumber($model->branch_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function generateNumber($id)
    {
        $branch = Branch::find($id)->alias_name;
        $string = 'ADJ'.date('y').'/'.date('m').'/'.$branch;
        $format = $string.'0000';
        $latest = self::where('branch_id',$id)
            ->whereMonth('created_at',date('m'))->orderBy('id','desc')->first();
        if($latest){
            $format = $latest->number;
        }
        $id = substr($format, -4);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 4, '0', STR_PAD_LEFT);
        return $string.$newID;
    }

    protected $appends = ['status_text'];

    public function getStatusTextAttribute()
    {
        if($this->status == 0){
            $status = 'New';
        }elseif($this->status == 1){
            $status = 'Approve';
        }elseif($this->status == 2){
            $status = 'Reject';
        }
        return $status;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function chart_of_account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function reason_correction()
    {
        return $this->belongsTo(ReasonCorrection::class, 'reason_correction_id');
    }

    public function detail_items()
    {
        return $this->hasMany(StockCorrectionDetail::class, 'stock_correction_id');
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
