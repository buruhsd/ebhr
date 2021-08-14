<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'agency',
        'address',
        'block',
        'no',
        'rt',
        'rw',
        'is_pkp',
        'is_closed',
        'npwp_id',
        'postal_code_id',
        'village_id',
        'district_id',
        'regency_id',
        'province_id',
        'insertedBy',
        'updatedBy',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->code = self::register_number();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function register_number()
    {
        $int = 9;
        $string = '000000-0';
        $latest = self::orderBy('id','desc')->first();
        if($latest){
            $string = str_replace('R','',$latest->code);
        }
        $id = substr($string,0,6);
        $newID = intval($id) + 1;
        $newID = str_pad($newID, 6, '0', STR_PAD_LEFT);
        $array = str_split($newID);
        $arrayNum = [1,3,5,7,1,3];
        $numTemp = 0;
        for ($i=0; $i < count($array); $i++) {
            $numTemp += $array[$i] * $arrayNum[$i];
        }

        $round = $numTemp/$int;
        $roundup = self::round_up($round,0);
        $random = ($roundup * $int) - $numTemp;
        return 'R'.$newID.'-'.$random;
    }

    public static function round_up($value, $places)
    {
        $mult = pow(10, abs($places));
        return $places < 0 ?
        ceil($value / $mult) * $mult :
            ceil($value * $mult) / $mult;
    }

    protected $appends = ['label','complete_address'];

    public function getLabelAttribute()
    {
        return $this->code.' - '.$this->name;
    }

    public function getCompleteAddressAttribute()
    {
        $block = $this->block ? ' Blok '.$this->block : '';
        $no = $this->no ? ' No '.$this->no : '';
        return $this->address.$block.$no.' RT '.$this->rt.' RW '.$this->rw;
    }

    public function npwp()
    {
        return $this->belongsTo(Npwp::class, 'npwp_id');
    }

    public function postal_code()
    {
        return $this->belongsTo(PostalCode::class, 'postal_code_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
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
