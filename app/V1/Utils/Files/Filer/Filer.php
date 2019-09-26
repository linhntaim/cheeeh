<?php

namespace App\V1\Utils\Files\Filer;

use App\V1\Utils\Files\Disk\Disk;
use App\V1\Utils\Files\Disk\LocalDisk;
use App\V1\Utils\Files\Disk\PublicDisk;

class Filer
{
    /**
     * @var Disk
     */
    protected $disk;

    /**
     * @var resource
     */
    protected $handler;

    public function __construct($file, $strict = true)
    {
        $this->disk = Disk::factory($file, $strict);
    }

    public function inFree()
    {
        return get_class($this->disk) === Disk::class;
    }

    public function inLocal()
    {
        return get_class($this->disk) === LocalDisk::class;
    }

    public function inPublic()
    {
        return get_class($this->disk) === PublicDisk::class;
    }

    public function getFile()
    {
        return $this->disk->getFile();
    }

    public function getBaseName()
    {
        return $this->disk->getFileBaseName();
    }

    public function getFileName()
    {
        return $this->disk->getFileName();
    }

    public function getSize()
    {
        return $this->disk->getFileSize();
    }

    public function getExtension()
    {
        return $this->disk->getFileExtension();
    }

    public function getRealPath()
    {
        return $this->disk->getFileRealPath();
    }

    public function getRealDirectory()
    {
        return $this->disk->getFileRealDirectory();
    }

    public function getResponse()
    {
        return response()->file($this->getRealPath());
    }

    public function delete()
    {
        $this->disk->fileDelete();
    }

    public function moveInLocal($directory = null, $name = null)
    {
        if (!$this->inLocal()) {
            $this->disk = (new LocalDisk())->fileMoveIn($this->disk, $directory, $name);
            return true;
        }
        return false;
    }

    public function moveInPublic($directory = null, $name = null)
    {
        if (!$this->inPublic()) {
            $this->disk = (new PublicDisk())->fileMoveIn($this->disk, $directory, $name);
            return true;
        }
        return false;
    }

    public function move($directory = null, $name = null)
    {
        $this->disk->fileMove($directory, $name);
    }

    public function duplicate($directory = null, $name = null)
    {
        $this->disk->fileDuplicate($directory, $name);
    }

    public function openToWrite()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->getRealPath(), 'w');
        }
        return $this;
    }

    public function openToAppend()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->getRealPath(), 'a');
        }
        return $this;
    }

    public function openToRead()
    {
        if (!is_resource($this->handler)) {
            $this->handler = fopen($this->getRealPath(), 'r');
        }
        return $this;
    }

    public function readAll()
    {
        if (is_resource($this->handler)) {
            return fread($this->handler, filesize($this->getRealPath()));
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

    public function __destruct()
    {
        $this->close();
    }
}
