<?php

namespace App\Models\Inventory;

use App\Traits\Observable;
use App\Models\User;
use App\Models\UserUnit;
use App\Models\Branch;
use App\Models\BpbType;
use App\Models\Organization;
use App\Models\UsageGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestItem extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'branch_id',
        'organization_id',
        'bpb_type_id',
        'usage_group_id',
        'user_unit_id',
        'number_spb',
        'date_spb',
        'number_pkb',
        'date_pkb',
        'note',
        'status',
        'approved_by',
        'approved_at',
        'closed_by',
        'closed_at',
        'insertedBy',
        'updatedBy',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->number_spb = self::generateNumber($model->branch_id,$model->bpb_type_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function generateNumber($id,$bpb_type_id)
    {
        $branch = Branch::find($id)->alias_name;
        $type = BpbType::find($bpb_type_id)->code;
        $string = 'SPB'.date('y').'/'.date('m').'/'.$branch.$type;
        $format = $string.'0000';
        $latest = self::where('branch_id',$id)
            ->where('bpb_type_id',$bpb_type_id)
            ->whereMonth('created_at',date('m'))->orderBy('id','desc')->first();
        if($latest){
            $format = $latest->number_spb;
        }
        $id = substr($format, -4);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 4, '0', STR_PAD_LEFT);
        return $string.$newID;
    }

    protected $appends = ['status_text','label'];

    public function getLabelAttribute()
    {
        return $this->number_spb;
    }

    public function getStatusTextAttribute()
    {
        if($this->status == 0){
            $status = 'New';
        }elseif($this->status == 1){
            $status = 'Approve';
        }elseif($this->status == 2){
            $status = 'Reject';
        }elseif($this->status == 3){
            $status = 'Close';
        }elseif($this->status == 4){
            $status = 'Done';
        }
        return $status;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function bpb_type()
    {
        return $this->belongsTo(BpbType::class, 'bpb_type_id');
    }

    public function usage_group()
    {
        return $this->belongsTo(UsageGroup::class, 'usage_group_id');
    }

    public function user_unit()
    {
        return $this->belongsTo(UserUnit::class, 'user_unit_id');
    }

    public function detail_items()
    {
        return $this->hasMany(RequestItemDetail::class, 'request_item_id');
    }

    public function expenditure()
    {
        return $this->hasMany(ProductExpenditure::class, 'request_item_id');
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function closed_by()
    {
        return $this->belongsTo(User::class, 'closed_by');
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
