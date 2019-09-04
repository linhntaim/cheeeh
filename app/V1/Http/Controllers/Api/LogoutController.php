<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;

class LogoutController extends ApiController
{
    public function logout()
    {
        return $this->responseSuccess();
    }
}
