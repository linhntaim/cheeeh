<?php

namespace App\V1\Utils\Files\Filer;

class BinaryFiler extends Filer
{
    public function openToWrite()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->getRealPath(), 'wb');
        }
        return $this;
    }

    public function openToAppend()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->getRealPath(), 'ab');
        }
        return $this;
    }

    public function openToRead()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->getRealPath(), 'rb');
        }
        return $this;
    }
}
