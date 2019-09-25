<?php

namespace App\V1\Utils\Files\Disk;

use App\V1\Utils\ClassTrait;
use App\V1\Utils\Files\File;
use Illuminate\Http\UploadedFile;

class Disk
{
    use ClassTrait;

    public static function factory($file)
    {
        $localDisk = new LocalDisk();
        if ($file instanceof UploadedFile) {
            return $localDisk->handle($file);
        }
        $file = File::from($file);
        $publicDisk = new PublicDisk();
        if ($publicDisk->in($file->getRealPath())) {
            return $publicDisk->handle($file);
        }
        if ($localDisk->in($file->getRealPath())) {
            return $localDisk->handle($file);
        }
        return (new Disk())->handle($file);
    }

    protected $directory;

    /**
     * @var File
     */
    protected $file;

    public function handle($file)
    {
        $this->file = File::from($file);
        return $this;
    }

    public function in($path)
    {
        return true;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getBaseName()
    {
        return $this->file->getBasename();
    }

    public function getFileName()
    {
        return pathinfo($this->getBaseName(), PATHINFO_FILENAME);
    }

    public function getSize()
    {
        return $this->file->getSize();
    }

    public function getExtension()
    {
        return $this->file->getExtension();
    }

    public function getRealPath()
    {
        return $this->file->getRealPath();
    }

    public function getRealDirectory()
    {
        return dirname($this->getRealPath());
    }

    public function exists($path)
    {
        return file_exists($path);
    }

    public function delete()
    {
        $path = $this->getRealPath();
        if (file_exists($path)) {
            unlink($path);
        }
        $this->file = null;
    }

    public function moveIn(Disk $fromDisk)
    {
        $this->handle($fromDisk->getFile());
        return $this;
    }

    public function move($directory = null, $name = null)
    {
        $this->file = $this->file->move($directory ?: $this->getRealPath(), $name);
    }
}
