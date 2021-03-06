<?php

namespace App\V1\Rules;

use Illuminate\Support\Facades\Hash;

class CurrentPasswordRule extends Rule
{
    public function __construct()
    {
        parent::__construct();

        $this->name = 'current_password';
    }

    public function passes($attribute, $value)
    {
        $user = request()->user();
        return empty($user) ? false : Hash::check($value, $user->password);
    }
}
