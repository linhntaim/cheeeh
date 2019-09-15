<?php

namespace App\V1\Utils\Files\FileWriter;

class BinaryFileWriter extends FileWriter
{
    public function openToWrite()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'wb');
        }
        return $this;
    }

    public function openToAppend()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'ab');
        }
        return $this;
    }

    public function openToRead()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'rb');
        }
        return $this;
    }
}
