<?php

namespace App\V1\Utils\Files\FileWriter;

use App\V1\Utils\Files\FileHelper;
use App\V1\Utils\Files\RelativeFileContainer;

class FileWriter extends RelativeFileContainer
{
    protected $filePath;
    protected $handler;

    public function __construct($name, $stored = false, $toDirectory = '', $isRelative = false)
    {
        $fileHelper = FileHelper::getInstance();
        $toDirectory = $stored ? $fileHelper->storePath()
            : (empty($toDirectory) ? $fileHelper->defaultPath()
                : $fileHelper->autoDirectory($toDirectory, $isRelative));
        $fileHelper->checkDirectory($toDirectory);
        $this->filePath = $fileHelper->concatPath(
            $toDirectory,
            $fileHelper->autoFilename($name)
        );
    }

    public function getRealPath()
    {
        return $this->filePath;
    }

    public function openToWrite()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'w');
        }
        return $this;
    }

    public function openToAppend()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'a');
        }
        return $this;
    }

    public function openToRead()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->filePath, 'r');
        }
        return $this;
    }

    public function readAll()
    {
        if (is_resource($this->handler)) {
            return fread($this->handler, filesize($this->filePath));
        }
        return null;
    }

    public function write($anything)
    {
        if (is_resource($this->handler)) {
            fwrite($this->handler, $anything);
        }
    }

    public function close()
    {
        if (is_resource($this->handler)) {
            fclose($this->handler);
        }

        return $this;
    }

    public function delete()
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
