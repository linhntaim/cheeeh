<?php

namespace App\V1\Models;

use App\V1\Configuration;
use App\V1\ModelTraits\MemorizeTrait;
use App\V1\Notifications\ResetPasswordNotification;
use App\V1\Utils\ConfigHelper;
use App\V1\Utils\CryptoJs\AES;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements HasLocalePreference
{
    use HasApiTokens, Notifiable, MemorizeTrait, SoftDeletes;

    const PROTECTED = [1, 2, 3];

    protected $table = 'users';

    protected $via = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'password',
        'url_avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getEmailAttribute()
    {
        if (!$this->memorizable('email')) {
            $this->memorize('email', $this->emails()->main()->first());
        }
        return $this->remind('email');
    }

    public function getEmailAddressAttribute()
    {
        return $this->email->email;
    }

    public function getEmailVerifiedAttribute()
    {
        return $this->email->verified;
    }

    public function getLocalizationAttribute()
    {
        if (!$this->memorizable('localization')) {
            $this->memorize('localization', $this->localizations()->first());
        }
        return $this->remind('localization');
    }

    public function getRolesAttribute()
    {
        if (!$this->memorizable('roles')) {
            $roles = $this->roles()->get();
            $roles->load('permissions');
            $this->memorize('roles', $roles);
        }
        return $this->remind('roles');
    }

    public function getRoleNamesAttribute()
    {
        if (!$this->memorizable('role_names')) {
            $roleNames = [];
            $this->roles->each(function ($role) use (&$roleNames) {
                $roleNames[] = $role->name;
            });
            $this->memorize('role_names', $roleNames);
        }
        return $this->remind('role_names');
    }

    public function getPermissionNamesAttribute()
    {
        if (!$this->memorizable('permission_names')) {
            $permissionNames = [];
            $this->roles->each(function ($role) use (&$permissionNames) {
                $role->permissions->each(function ($permission) use (&$permissionNames) {
                    if (!in_array($permission->name, $permissionNames)) {
                        $permissionNames[] = $permission->name;
                    }
                });
            });
            $this->memorize('permission_names', $permissionNames);
        }
        return $this->remind('permission_names');
    }

    public function emails()
    {
        return $this->hasMany(UserEmail::class, 'user_id', 'id');
    }

    public function phones()
    {
        return $this->hasMany(UserPhone::class, 'user_id', 'id');
    }

    public function localizations()
    {
        return $this->hasMany(UserLocalization::class, 'user_id', 'id');
    }

    public function socials()
    {
        return $this->hasMany(UserSocial::class, 'user_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users', 'user_id', 'role_id');
    }

    public function hasPermission($permissionName)
    {
        return in_array($permissionName, $this->permissionNames);
    }

    public function hasPermissionsAtLeast($permissionNames)
    {
        foreach ($permissionNames as $permissionName) {
            if ($this->hasPermission($permissionName)) return true;
        }
        return false;
    }

    #region CanResetPassword
    public function getEmailForPasswordReset()
    {
        return $this->emailAddress;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    #endregion

    #region HasLocalePreference
    public function preferredLocale()
    {
        return $this->localization->locale;
    }
    #endregion

    public function preferredEmail()
    {
        return $this->email->email;
    }

    public function preferredName()
    {
        return $this->display_name;
    }

    #region Passport
    public function findForPassport($username)
    {
        if (request()->has('_e')) {
            $username = AES::decrypt($username, ConfigHelper::getClockBlockKey());
        }
        $advanced = json_decode($username);
        if ($advanced !== false) {
            if (!empty($advanced->provider) && !empty($advanced->provider_id)) {
                $provider = $advanced->provider;
                $providerId = $advanced->provider_id;
                $user = User::whereHas('socials', function ($query) use ($provider, $providerId) {
                    $query->where('provider', $provider)
                        ->where('provider_id', $providerId);
                })->first();
                if ($user) $user->via = 'social';
                return $user;
            }
            if (!empty($advanced->token) && !empty($advanced->id)) {
                $sysToken = SysToken::where('type', SysToken::TYPE_LOGIN)
                    ->where('token', $advanced->token)->first();
                if (!empty($sysToken)) {
                    $sysToken->delete();
                    $user = User::where('id', $advanced->id)
                        ->orWhereHas('emails', function ($query) use ($advanced) {
                            $query->where('email', $advanced->id);
                        })
                        ->orWhere('name', $advanced->id)
                        ->first();
                    if ($user) $user->via = 'token';
                    return $user;
                }
            }
        }
        return User::whereHas('emails', function ($query) use ($username) {
            $query->where('email', $username);
        })
            ->orWhere('name', $username)
            ->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        if (request()->has('_e')) {
            $password = AES::decrypt($password, ConfigHelper::getClockBlockKey());
        }
        $advanced = json_decode($password);
        if ($advanced !== false) {
            if (!empty($advanced->source) && !empty($this->via)
                && $advanced->source == $this->via) return true;
        }
        return Hash::check($password, $this->password);
    }
    #endregion
}
