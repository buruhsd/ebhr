<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'scu_code_tipe'
    ];
}
