<?php

namespace App\V1\Exports;

trait HeaderTrait
{
    protected function header()
    {
        return $this::HEADER;
    }

    /**
     * @param callable|null $callback
     * @return array
     */
    protected function getHeader($callback = null)
    {
        $header = $this->header();
        return $callback ? $callback($header) : $header;
    }
}
