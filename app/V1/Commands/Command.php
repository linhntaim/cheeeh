<?php

namespace App\V1\Commands;

use App\V1\Utils\ClassTrait;
use Illuminate\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    use ClassTrait;

    protected $friendlyName;

    public function getFriendlyName()
    {
        if (empty($this->friendlyName)) {
            $this->friendlyName = $this->__friendlyClassBaseName();
        }
        return $this->friendlyName;
    }

    protected function before()
    {
        $this->info(sprintf('START %s...', strtoupper($this->getFriendlyName())));
    }

    protected function after()
    {
        $this->info(sprintf('END %s!!!', strtoupper($this->getFriendlyName())));
    }
}
