<?php

namespace App\V1\Commands;

use Illuminate\Support\Facades\Artisan;

class SetupCommand extends Command
{
    protected $signature = 'setup';

    public function handle()
    {
        $this->before();
        Artisan::call('migrate', [
            '--seed' => true,
        ]);
        Artisan::call('passport:install', [
            '--force' => true,
        ]);
        $this->after();
    }
}
