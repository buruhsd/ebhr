<?php

namespace App\Models\Purchase;

use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'purchase_letter_id',
        'purchase_letter_item_id',
        'transaction_type_id',
        'supplier_id',
        'no_op',
        'date_op',
        'date_estimate',
        'ppn',
        'qty',
        'price',
        'discount',
        'net',
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
                $model->no_op = self::numberOP($model->branch_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
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

    public function purchase_letter()
    {
        return $this->belongsTo(PurchaseLetter::class, 'purchase_letter_id');
    }

    public function item()
    {
        return $this->belongsTo(PurchaseLetterItem::class, 'purchase_letter_item_id');
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

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insertedBy');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
}
