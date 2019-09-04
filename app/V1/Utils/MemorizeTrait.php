<?php

namespace App\V1\Utils;

trait MemorizeTrait
{
    protected $memories = [];

    protected function memorizable($key)
    {
        return isset($this->memories[$key]);
    }

    protected function remind($key)
    {
        return isset($this->memories[$key]) ? $this->memories[$key] : null;
    }

    public function memorize($key, $value)
    {
        $this->memories[$key] = $value;
        return $value;
    }
}
