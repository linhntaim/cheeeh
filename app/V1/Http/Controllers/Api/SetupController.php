<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Artisan;

class SetupController extends ApiController
{
    public function setup()
    {
        Artisan::call('setup');

        return $this->responseSuccess();
    }
}
