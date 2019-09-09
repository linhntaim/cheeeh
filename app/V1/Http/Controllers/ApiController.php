<?php

namespace App\V1\Http\Controllers;

class ApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->withThrottlingMiddleware();
    }
}
