<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_code',
        'name',
        'insertedBy',
        'updatedBy',
    ];
    protected $table = 'products';

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->product_code." - ".$this->name;
    }

}
