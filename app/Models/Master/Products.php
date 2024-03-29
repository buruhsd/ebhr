<?php

namespace App\Models\Master;

use App\Traits\Observable;
use App\Models\User;
use App\Models\LimitStock;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Purchase\PurchaseLetterItem;
use App\Models\Inventory\RequestItemDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'register_number',
        'product_code',
        'name',
        'second_name',
        'spesification',
        'product_number',
        'type',
        'brand',
        'vendor',
        'barcode',
        'status',
        'is_approve',
        'unit_id',
        'category_id',
        'insertedBy',
        'updatedBy',
    ];
    protected $table = 'products';
    protected $appends = ['label'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->register_number = self::register_number();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function serial_number()
    {
        return $this->hasOne(ProductSerialNumber::class, 'product_id');
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'insertedBy');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }

    public static function register_number()
    {
        $int = 9;
        $string = '0000000-0';
        $latest = self::orderBy('id','desc')->first();
        if($latest){
            $string = $latest->register_number;
        }

        $id = substr($string,0,7);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 7, '0', STR_PAD_LEFT);
        $array = str_split($newID);
        $arrayNum = [1,3,5,7,1,3,5];
        $numTemp = 0;
        for ($i=0; $i < count($array); $i++) {
            $numTemp += $array[$i] * $arrayNum[$i];
        }

        $round = $numTemp/$int;
        $roundup = self::round_up($round,0);
        $random = ($roundup * $int) - $numTemp;
        return $newID.'-'.$random;
    }

    public static function round_up($value, $places)
    {
        $mult = pow(10, abs($places));
        return $places < 0 ?
        ceil($value / $mult) * $mult :
            ceil($value * $mult) / $mult;
    }

    public function minmax()
    {
        return $this->hasOne(LimitStock::class, 'product_id')
            ->whereDate('expired_at','<=', date('Y-m-d'))->orderBy('id','desc');
    }

    public function min_max()
    {
        return $this->hasMany(LimitStock::class, 'product_id');
    }

    public function items_pp()
    {
        return $this->hasMany(PurchaseLetterItem::class, 'product_id')
            ->whereHas('purchase', function ($query){
                $query->whereIn('purchase_letters.status',[0,1]);
            });
    }

    public function items_op()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'product_id')
            ->whereHas('purchase_order', function ($query){
                $query->whereIn('purchase_orders.status',[0,1,3,6]);
            });
    }

    public function items_spb()
    {
        return $this->hasMany(RequestItemDetail::class, 'product_id')
            ->whereHas('request_item', function ($query){
                $query->doesntHave('expenditure');
            });
    }
}
