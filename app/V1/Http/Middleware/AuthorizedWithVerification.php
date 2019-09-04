<?php

namespace App\V1\Http\Middleware;

use App\V1\Http\Requests\Request;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthorizedWithVerification
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, $method = null)
    {
        $user = $this->auth->user();
        switch ($method) {
            case 'email':
                if (empty($user->email) || !$user->email->verified) {
                    return abort(403);
                }
                break;
        }

        return $next($request);
    }
}
