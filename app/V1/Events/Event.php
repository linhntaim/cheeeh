<?php

namespace App\V1\Events;

use App\V1\Utils\ClientAppTrait;

abstract class Event
{
    use ClientAppTrait;

    public function __construct()
    {
        $this->initClientApp();
    }
}
