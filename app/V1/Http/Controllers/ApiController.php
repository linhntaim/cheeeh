<?php

namespace App\V1\Http\Controllers;

use App\V1\Http\Requests\Request;
use App\V1\Utils\ClientAppHelper;
use App\V1\Utils\LocalizationHelper;
use Closure;

class ApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            LocalizationHelper::getInstance()->autoFetch($request);
            ClientAppHelper::getInstance();
            return $next($request);
        });
    }
}
