<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'trx_code',
        'trx_urut',
        'trx_date',
        'trx_jenis',
        'trx_dbcr',
        'scu_code',
        'inv_code',
        'loc_code',
        'statusProduct',
        'trx_kuan',
        'hargaSatuan',
        'trx_amnt',
        'trx_totl',
        'trx_hpok',
        'trx_havg',
        'pos_date',
        'sal_code',
        'scu_code_tipe',
        'branch_id'
    ];

    protected static function boot()
    {
        parent::boot(); //

        self::creating(function ($model) {
            $model->branch_id = Auth::User()->branch_id;
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id')->select('id','name','alias_name','code');
    }
}
