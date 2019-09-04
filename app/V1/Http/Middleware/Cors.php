<?php

namespace App\V1\Http\Middleware;

use Closure;
use App\V1\Http\Requests\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Accept, Application, Authorization, Content-Type, Localization, Origin, Referer, User-Agent, X-Authorization');
        $response->header('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, DELETE');
        return $response;
    }
}
