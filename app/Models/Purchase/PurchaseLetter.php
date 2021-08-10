<?php

namespace App\Models\Purchase;

use App\Models\Branch;
use App\Models\Master\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase\PurchaseLetterItem;

class PurchaseLetter extends Model
{
    use HasFactory;
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
        'insertedBy',
        'updatedBy',
    ];

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
        // PPB2021/08/014
        $branch = Branch::find($id)->code;
        $string = 'PPB'.date('Y').'/'.date('m').'/'.$branch;
        $format = $string.'0';
        $latest = self::orderBy('id','desc')->first();
        if($latest){
            $format = $latest->no_pp;
        }
        $id = substr($format, 13,strlen($format)-13);
        $newID = intval($id) + 1;
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
}
