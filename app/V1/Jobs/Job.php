<?php

namespace App\V1\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job extends NowJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
}
