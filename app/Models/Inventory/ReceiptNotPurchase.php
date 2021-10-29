<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Branch;
use App\Models\PbpType;
use App\Models\Master\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptNotPurchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'warehouse_id',
        'pbp_type_id',
        'product_expenditure_id',
        'original_warehouse_id',
        'number',
        'date',
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
                $model->number = self::generateNumber($model->branch_id,$model->pbp_type_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function generateNumber($id,$type_id)
    {
        $branch = Branch::find($id)->alias_name;
        $type = PbpType::find($type_id)->code;
        $string = 'PBP'.date('y').'/'.date('m').'/'.$branch.$type;
        $format = $string.'0000';
        $latest = self::where('branch_id',$id)
            ->where('pbp_type_id',$type_id)
            ->whereMonth('created_at',date('m'))->orderBy('id','desc')->first();
        if($latest){
            $format = $latest->number_bpb;
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

    public function expenditure()
    {
        return $this->belongsTo(ProductExpenditure::class, 'product_expenditure_id');
    }

    public function pbp_type()
    {
        return $this->belongsTo(PbpType::class, 'pbp_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function original_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'original_warehouse_id');
    }

    public function detail_items()
    {
        return $this->hasMany(ReceiptNotPurchaseDetail::class, 'receipt_not_purchase_id');
    }

    public function detail_serial_items()
    {
        return $this->hasMany(ReceiptNotPurchaseDetail::class, 'receipt_not_purchase_id')->where(['is_serial_number'=>1,'status'=>0]);
    }

    public function detail_return_items()
    {
        return $this->hasMany(ReceiptNotPurchaseDetail::class, 'receipt_not_purchase_id')->where('is_return',1);
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
