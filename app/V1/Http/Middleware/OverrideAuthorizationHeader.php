<?php

namespace App\V1\Http\Middleware;

use Closure;
use App\V1\Http\Requests\Request;

class OverrideAuthorizationHeader
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->headers->has('X-Authorization')) {
            $request->headers->set('Authorization', $request->headers->get('X-Authorization'));
        }
        return $next($request);
    }
}
