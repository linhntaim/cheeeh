<?php

namespace App\V1\Models;

use App\V1\ModelTraits\ArrayValuedAttributesTrait;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use ArrayValuedAttributesTrait;

    const PROVIDER_SERVER = 'server';
    const PROVIDER_BROWSER = 'browser';
    const PROVIDER_IOS = 'ios';
    const PROVIDER_ANDROID = 'android';

    protected $table = 'devices';

    protected $fillable = [
        'user_id',
        'provider',
        'secret',
        'client_ip',
        'meta',
        'meta_array_value',
        'meta_overridden_array_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
