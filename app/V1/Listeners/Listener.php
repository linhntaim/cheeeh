<?php

namespace App\V1\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Listener implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;
}
