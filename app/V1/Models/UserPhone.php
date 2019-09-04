<?php

namespace App\V1\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserPhone extends Model
{
    const IS_ALIAS_YES = 1;
    const IS_ALIAS_NO = 2;

    protected $table = 'user_phones';

    protected $fillable = [
        'user_id',
        'email',
        'is_alias',
        'verified_code',
        'verified_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->verified_code = Str::random(32);
        });
    }
}
