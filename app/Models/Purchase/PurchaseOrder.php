<?php

namespace App\Models\Purchase;

use App\Models\Branch;
use App\Models\Currency;
use App\Models\KursType;
use App\Models\Supplier;
use App\Models\Master\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'transaction_type_id',
        'supplier_id',
        'no_op',
        'date_op',
        'date_estimate',
        'ppn',
        'ppn_hc',
        'dpp',
        'total',
        'max_price_unit',
        'max_price_item',
        'currency_id',
        'kurs_type_id',
        'kurs',
        'status',
        'noted',
        'approved_by',
        'approved_at',
        'released_by',
        'released_at',
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
                $model->no_op = self::numberOP($model->branch_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public function getKursAttribute()
    {
        return round($this->attributes['kurs'],2);
    }

    public static function numberOP($id)
    {
        $branch = Branch::find($id)->alias_name;
        $string = 'OPB'.date('y').'/'.date('m').'/'.$branch;
        $format = $string.'0000';
        $latest = self::where('branch_id',$id)
            ->whereMonth('created_at',date('m'))->orderBy('id','desc')->first();
        if($latest){
            $format = $latest->no_op;
        }
        $id = substr($format, -4);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 4, '0', STR_PAD_LEFT);
        return $string.$newID;
    }

    public function kurs_type()
    {
        return $this->belongsTo(KursType::class, 'kurs_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function description()
    {
        return $this->hasOne(PurchaseDescription::class, 'purchase_order_id');
    }

    public function order_item()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
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
