<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'insertedBy',
        'updatedBy',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                if($model->parent_id){
                    $model->code = self::code($model->parent_id,$model->code );
                }
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function code($parent_id,$code)
    {
        $parent = self::find($parent_id);
        return $parent->code.$code;
    }

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function childs()
    {
        return $this->hasMany(SupplierCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(SupplierCategory::class, 'parent_id');
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
