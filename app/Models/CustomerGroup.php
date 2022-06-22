<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory,Observable;
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
        return $this->hasMany(CustomerGroup::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(CustomerGroup::class, 'parent_id');
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
