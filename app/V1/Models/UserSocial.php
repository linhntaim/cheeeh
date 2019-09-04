<?php

namespace App\V1\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    protected $table = 'user_socials';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];
}
