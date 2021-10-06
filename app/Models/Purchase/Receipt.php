<?php

namespace App\Models\Purchase;

use App\Models\User;
use App\Models\Branch;
use App\Models\Master\Warehouse;
use App\Models\Currency;
use App\Models\KursType;
use App\Models\Supplier;
use App\Models\Master\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Receipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'warehouse_id',
        'purchase_order_id',
        'supplier_id',
        'number',
        'date',
        'ppn',
        'term_of_payment',
        'currency_id',
        'kurs_type_id',
        'kurs',
        'ppn_valas',
        'ppn_idr',
        'total_valas',
        'total_idr',
        'dpp',
        'status',
        'noted',
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

    protected $appends = ['status_text'];

    public function getKursAttribute()
    {
        return round($this->attributes['kurs'],2);
    }

    public function getStatusTextAttribute()
    {
        if($this->status == 0){
            $status = '-';
        }
        return $status;
    }

    public static function generateNumber($id)
    {
        $branch = Branch::find($id)->alias_name;
        $string = 'TTB'.date('y').'/'.date('m').'/'.$branch;
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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function kurs_type()
    {
        return $this->belongsTo(KursType::class, 'kurs_type_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function receipt_items()
    {
        return $this->hasMany(ReceiptItems::class, 'receipt_id');
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
