<?php

namespace App\V1\Http\Controllers\Api\Admin;

use App\V1\Http\Controllers\Api\UserEmailController as BaseUserEmailController;
use App\V1\Http\Requests\Request;

class UserEmailController extends BaseUserEmailController
{
    protected function search(Request $request)
    {
        $search = [];
        $input = $request->input('user_id');
        if (!empty($input)) {
            $search['user_id'] = $input;
        }
        return $search;
    }
}
