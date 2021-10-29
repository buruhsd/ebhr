<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Branch;
use App\Models\BpbType;
use App\Models\Master\Warehouse;
use App\Models\Inventory\RequestItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExpenditure extends Model
{
    // status 0 => belum di buatkan nomer seri, 1 => sudah dibuatkan nomor seri , 2 => dibuatkan penerimaan barang bukan pembelian
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'request_item_id',
        'bpb_type_id',
        'warehouse_id',
        'destination_warehouse_id',
        'number_bpb',
        'date_bpb',
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
                $model->number_bpb = self::generateNumber($model->branch_id,$model->request_item_id);
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function generateNumber($id,$requestItemId)
    {
        $branch = Branch::find($id)->alias_name;
        $requestItem = RequestItem::find($requestItemId);
        $type = BpbType::find($requestItem->bpb_type_id)->code;
        $string = 'BPB'.date('y').'/'.date('m').'/'.$branch.$type;
        $format = $string.'0000';
        $latest = self::where('branch_id',$id)
            ->where('bpb_type_id',$requestItem->bpb_type_id)
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

    public function request_item()
    {
        return $this->belongsTo(RequestItem::class, 'request_item_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function bpb_type()
    {
        return $this->belongsTo(BpbType::class, 'bpb_type_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function destination_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function detail_items()
    {
        return $this->hasMany(ProductExpenditureDetail::class, 'product_expenditure_id');
    }

    public function detail_serial_items()
    {
        return $this->hasMany(ProductExpenditureDetail::class, 'product_expenditure_id')->where(['is_serial_number'=>1,'status'=>0]);
    }

    public function detail_return_items()
    {
        return $this->hasMany(ProductExpenditureDetail::class, 'product_expenditure_id')->where('is_return',1);
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
