<?php

namespace App\V1\Http\Requests;

use App\V1\Utils\Helper;
use Illuminate\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    public function input($key = null, $default = null)
    {
        if (is_null($key)) {
            return parent::input($key);
        }
        return Helper::default(parent::input($key), $default);
    }
}
