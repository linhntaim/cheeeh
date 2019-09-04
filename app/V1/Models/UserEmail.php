<?php

namespace App\V1\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserEmail extends Model
{
    const IS_ALIAS_YES = 1;
    const IS_ALIAS_NO = 2;

    protected $table = 'user_emails';

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

    public function scopeMain($query)
    {
        return $query->where('is_alias', static::IS_ALIAS_NO);
    }

    public function scopeNotMain($query)
    {
        return $query->where('is_alias', static::IS_ALIAS_YES);
    }

    public function getIsAliasAttribute()
    {
        return $this->attributes['is_alias'] == static::IS_ALIAS_YES;
    }

    public function getVerifiedAttribute()
    {
        return !empty($this->attributes['verified_at']);
    }

    public function getNotVerifiedAttribute()
    {
        return empty($this->attributes['verified_at']);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
