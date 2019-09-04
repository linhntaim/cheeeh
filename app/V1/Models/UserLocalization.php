<?php

namespace App\V1\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocalization extends Model
{
    protected $table = 'user_localizations';

    protected $fillable = [
        'locale',
        'country',
        'timezone',
        'currency',
        'number_format',
        'first_day_of_week',
        'long_date_format',
        'short_date_format',
        'long_time_format',
        'short_time_format',
    ];

    public function getTimestampedUpdatedAtAttribute()
    {
        return strtotime($this->attributes['updated_at']);
    }
}
