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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->product_code = self::codeProduct();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function codeProduct()
    {
        $string = 'BRG-00000';
        $latest = self::orderBy('id','desc')->first();
        if($latest){
            $string = $latest->product_code;
        }
        $id = substr($string, -5, 5);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 5, '0', STR_PAD_LEFT);
        return 'BRG-'.$newID;
    }

}
