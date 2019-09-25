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

    public function __construct($file)
    {
        $this->disk = Disk::factory($file);
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
        return $this->disk->getBaseName();
    }

    public function getFileName()
    {
        return $this->disk->getFileName();
    }

    public function getSize()
    {
        return $this->disk->getSize();
    }

    public function getExtension()
    {
        return $this->disk->getExtension();
    }

    public function getRealPath()
    {
        return $this->disk->getRealPath();
    }

    public function getRealDirectory()
    {
        return $this->disk->getRealDirectory();
    }

    public function getResponse()
    {
        return response()->file($this->getRealPath());
    }

    public function delete()
    {
        $this->disk->delete();
    }

    public function moveInLocal($relativeDirectory = null)
    {
        if (!$this->inLocal()) {
            $this->disk = (new LocalDisk())->moveIn($this->disk);
        }
    }

    public function moveInPublic($relativeDirectory = null)
    {
        if (!$this->inPublic()) {
            $this->disk = (new PublicDisk())->moveIn($this->disk);
        }
    }
}
