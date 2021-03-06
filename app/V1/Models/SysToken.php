<?php

namespace App\V1\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SysToken extends Model
{
    const TYPE_LOGIN = 1;

    protected $table = 'sys_tokens';

    protected $fillable = [
        'token',
        'type',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->token = Str::random(128);
        });
    }
}
