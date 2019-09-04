<?php

namespace App\V1\Utils;

use App\V1\Models\User;

class DateTimeHelper extends BaseDateTimeHelper
{
    /**
     * @param integer|User $user
     * @return DateTimeHelper
     */
    public static function fromUser(User $user)
    {
        return new static((new LocalizationHelper())->fetchFromUser($user));
    }
}
