<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'model',
        'action',
        'message',
        'models'
    ];

    protected $casts = [
        'models' => 'array',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
