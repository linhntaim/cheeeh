<?php

namespace App\V1\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class Notification extends NowNotification implements ShouldQueue
{
    use Queueable;

    const NAME = 'notification';

    public function __construct($fromUser = null)
    {
        parent::__construct($fromUser);
    }
}
