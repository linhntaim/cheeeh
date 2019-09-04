<?php

namespace App\V1\Http\Middleware;

use App\V1\Http\Requests\Request;
use App\V1\Utils\AbortTrait;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthorizedWithPermissions
{
    use AbortTrait;

    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, $permissions = null)
    {
        if (empty($permissions)) return $next($request);

        $user = $this->auth->user();

        $prePermissions = explode('|', $permissions);
        foreach ($prePermissions as $prePermission) {
            $parts = explode('!', $prePermission); // first: permission or first: branch method, second: permission
            if (count($parts) == 1) {
                if ($user->hasPermission($parts[0])) {
                    return $next($request);
                }
            } else {
                if ($request->has($parts[0])) {
                    if ($user->hasPermissionsAtLeast(explode('#', $parts[1]))) {
                        return $next($request);
                    } else {
                        return $this->abort403();
                    }
                }
            }
        }

        return $this->abort403();
    }
}
