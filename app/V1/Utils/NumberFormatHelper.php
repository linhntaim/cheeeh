<?php

namespace App\V1\Utils;

use App\V1\Models\User;

class NumberFormatHelper extends BaseNumberFormatHelper
{
    /**
     * @param integer|User $user
     * @return NumberFormatHelper
     */
    public static function fromUser($user)
    {
        return new static((new LocalizationHelper())->fetchFromUser($user));
    }
}
