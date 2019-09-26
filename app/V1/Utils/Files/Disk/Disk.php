<?php

namespace App\V1\Utils\Files\Disk;

use App\V1\Exceptions\AppException;
use App\V1\Utils\ClassTrait;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Files\File;
use App\V1\Utils\Files\FileHelper;
use App\V1\Utils\Helper;
use App\V1\Utils\StringHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Disk
{
    use ClassTrait;

    public static function factory($file, $strict = true)
    {
        $localDisk = new LocalDisk();
        if ($file instanceof UploadedFile) {
            return $localDisk->handle($file, $strict);
        }
        $file = File::from($file);
        $publicDisk = new PublicDisk();
        if ($publicDisk->in($file->getRealPath())) {
            return $publicDisk->handle($file, $strict);
        }
        if ($localDisk->in($file->getRealPath())) {
            return $localDisk->handle($file, $strict);
        }
        return (new Disk())->handle($file, $strict);
    }

    protected $directory;

    /**
     * @var File
     */
    protected $file;

    public function getDirectory()
    {
        return $this->directory;
    }

    public function in($path)
    {
        return true;
    }

    public function exists($path)
    {
        return file_exists($path);
    }

    public function findFile($path, $fileBaseName, $strict = false, $recursive = false)
    {
        if (is_dir($path)) {
            foreach (scandir($path) as $item) {
                if ($item == '.' || $item == '..') continue;
                $itemPath = $path . DIRECTORY_SEPARATOR . $item;
                if (is_file($itemPath)) {
                    if ($strict) {
                        if ($fileBaseName == $item) {
                            return $itemPath;
                        }
                    } elseif (Str::contains($item, $fileBaseName)) {
                        return $itemPath;
                    }
                } else {
                    if ($recursive) {
                        if (($itemPath = $this->findFile($itemPath, $fileBaseName, $strict, $recursive)) !== false) {
                            return $itemPath;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function handle($file, $strict = true)
    {
        $this->file = File::from($file, $strict);
        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getFileBaseName()
    {
        return $this->file->getBasename();
    }

    public function getFileName()
    {
        return pathinfo($this->getFileBaseName(), PATHINFO_FILENAME);
    }

    public function getFileSize()
    {
        return $this->file->getSize();
    }

    public function getFileExtension()
    {
        return $this->file->getExtension();
    }

    public function getFileRealPath()
    {
        return $this->file->getRealPath();
    }

    public function getFileRelativePath()
    {
        return empty($this->directory) ?
            $this->getFileRealPath() : substr($this->getFileRealPath(), strlen($this->directory));
    }

    public function getFileRealDirectory()
    {
        return dirname($this->getFileRealPath());
    }

    public function getFileRelativeDirectory()
    {
        return empty($this->directory) ?
            $this->getFileRealDirectory() : substr($this->getFileRealDirectory(), strlen($this->directory));
    }

    public function fileDelete()
    {
        FileHelper::removeFile($this->getFileRealPath());
        $this->file = null;
    }

    public function fileDeleteFolder()
    {
        FileHelper::removeDirectory($this->getFileRealDirectory());
        $this->file = null;
    }

    public function fileExists()
    {
        return file_exists($this->getFileRealPath());
    }

    public function fileMoveIn(Disk $fromDisk, $fileDirectory = null, $fileBaseName = null)
    {
        if (!$fromDisk->fileExists()) {
            throw new AppException(static::__transErrorWithModule('file_not_found'));
        }
        $this->handle($fromDisk->getFile());
        $this->fileMove($fileDirectory, $fileBaseName);
        return $this;
    }

    public function fileMove($fileDirectory = null, $fileBaseName = null)
    {
        if (!$this->fileExists()) {
            throw new AppException(static::__transErrorWithModule('file_not_found'));
        }
        if (!empty($fileDirectory) || !empty($fileBaseName)) {
            $this->file = $this->file->move($this->fileAutoDirectory($fileDirectory), $this->fileAutoBaseName($fileBaseName));
        }
        return $this;
    }

    public function fileDuplicate($fileDirectory = null, $fileBaseName = null)
    {
        if (!$this->fileExists()) {
            throw new AppException(static::__transErrorWithModule('file_not_found'));
        }
        if (!empty($fileDirectory) || !empty($fileBaseName)) {
            $this->file = $this->file->copy($this->fileAutoDirectory($fileDirectory), $this->fileAutoBaseName($fileBaseName));
        }
        return $this;
    }

    public function fileAutoBaseName($fileBaseName)
    {
        if ($fileBaseName === true) return $this->getAlwaysFileBaseName();
        return $fileBaseName ?: $this->getFileBaseName();
    }

    public function fileAutoDirectory($fileDirectory)
    {
        if ($fileDirectory === true) return $this->getAlwaysDirectory();
        return $fileDirectory ?: $this->getFileRelativeDirectory();
    }

    protected function getAlwaysFileBaseName()
    {
        return FileHelper::randomFileBaseName($this->getFileExtension());
    }

    protected function getAlwaysDirectory()
    {
        $now = DateTimeHelper::syncNowObject();
        $hash = function ($value) {
            return dechex(intval(StringHelper::repeat(9, strlen($value))) - intval($value));
        };
        return implode(DIRECTORY_SEPARATOR, [
            $hash($now->format('Ym')),
            $hash($now->format('dH')),
            $hash(Helper::currentUserId(rand(0, $this->maxFirstFolder - 1)) % $this->maxFirstFolder + 170),
        ]);
    }
}
