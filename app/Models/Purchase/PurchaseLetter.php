<?php

namespace App\Models\Purchase;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Branch;
use App\Models\Master\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase\PurchaseLetterItem;

class PurchaseLetter extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'tgl_pp',
        'no_pp',
        'note',
        'branch_id',
        'warehouse_id',
        'transaction_type_id',
        'purchase_category_id',
        'purchase_necessary_id',
        'purchase_urgensity_id',
        'is_order',
        'status',
        'closed_by',
        'closed_at',
        'insertedBy',
        'updatedBy',
    ];

    protected $appends = ['label','status_text'];
    public function getLabelAttribute()
    {
        return $this->no_pp;
    }

    public function getStatusTextAttribute()
    {
        if($this->status == 0){
            $status ='New';
        }else if($this->status == 1){
            $status ='On Proses';
        }else if($this->status == 2){
            $status ='Done';
        }else if($this->status == 3){
            $status ='Close';
        }
        return $status;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->no_pp = self::numberPP($model->branch_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function numberPP($id)
    {
        // PPB21/08/014
        $branch = Branch::find($id)->alias_name;
        $string = 'PPB'.date('y').'/'.date('m').'/'.$branch;
        $format = $string.'0000';
        $latest = self::where('branch_id',$id)
            ->whereMonth('created_at',date('m'))->orderBy('id','desc')->first();
        if($latest){
            $format = $latest->no_pp;
        }
        // $id = substr($format, 11,strlen($format)-11);
        $id = substr($format, -4);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 4, '0', STR_PAD_LEFT);
        return $string.$newID;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function purchase_items()
    {
        return $this->hasMany(PurchaseLetterItem::class);
    }

    public function purchase_category()
    {
        return $this->belongsTo(PurchaseCategory::class, 'purchase_category_id');
    }

    public function purchase_necessary()
    {
        return $this->belongsTo(PurchaseNecessary::class, 'purchase_necessary_id');
    }

    public function purchase_urgentity()
    {
        return $this->belongsTo(PurchaseUrgentity::class, 'purchase_urgensity_id');
    }

    public function orders()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_letter_id');
    }

    public function closedBy()
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
