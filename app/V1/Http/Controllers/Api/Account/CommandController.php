<?php

namespace App\V1\Http\Controllers\Api\Account;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use Illuminate\Support\Facades\Artisan;

class CommandController extends ApiController
{
    public function index()
    {
        return $this->responseSuccess([
            'commands' => Artisan::all(),
        ]);
    }

    public function run(Request $request)
    {
        Artisan::call($request->input('cmd'), $request->input('params'));
        return $this->responseSuccess([
            'output' => Artisan::output(),
        ]);
    }
}
