<?php

namespace App\V1\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

abstract class NowJob
{
    use Dispatchable;

    public abstract function handle();
    public abstract function failed();
}
