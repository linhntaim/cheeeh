<?php

namespace App\V1\Exports;

abstract class Export
{
    const NAME = 'export';

    public function getName()
    {
        return $this::NAME;
    }

    protected function getFileNamePrefix()
    {
        return $this::NAME . '_';
    }

    public abstract function export();
}
