<?php

namespace App\Models\Accounting;

use App\Traits\Observable;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory,Observable;
    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'level',
        'normal_balance',
        'detail_general',
        'classification',
        'currency_id',
        'is_close',
        'insertedBy',
        'updatedBy',
    ];

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->code.' - '.$this->name;
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id')->with('children');
    }

    public function valas()
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
