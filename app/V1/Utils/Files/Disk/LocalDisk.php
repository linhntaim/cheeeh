<?php

namespace App\V1\Utils\Files\Disk;

use App\V1\Exceptions\AppException;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Files\File;
use App\V1\Utils\Files\FileHelper;
use App\V1\Utils\Helper;
use App\V1\Utils\StringHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalDisk extends Disk
{
    const MAX_FIRST_FOLDER = 1000;

    protected $disk;
    protected $maxFirstFolder;

    public function __construct($diskName = 'local', $maxFirstFolder = LocalDisk::MAX_FIRST_FOLDER)
    {
        $this->disk = Storage::disk($diskName);
        $this->directory = $this->disk->getAdapter()->getPathPrefix();
        $this->maxFirstFolder = $maxFirstFolder;
    }

    public function in($path)
    {
        return Str::startsWith($path, $this->directory);
    }

    public function exists($path)
    {
        return $this->disk->exists($path);
    }

    public function findFile($path, $fileBaseName, $strict = false, $recursive = false)
    {
        return parent::findFile($this->directory . DIRECTORY_SEPARATOR . $path, $fileBaseName, $strict, $recursive);
    }

    public function handle($file, $strict = true)
    {
        if ($file instanceof UploadedFile) {
            $relativeFilePath = $this->disk->putFile('', $file);
            $this->file = new File(FileHelper::concatPath(
                $this->directory, $relativeFilePath
            ));
            return $this;
        }
        return parent::handle($file, $strict);
    }

    public function fileDelete()
    {
        $this->disk->delete($this->getFileRelativePath());
    }

    public function fileDeleteFolder()
    {
        $this->disk->deleteDirectory($this->getFileRelativeDirectory());
    }

    public function fileMoveIn(Disk $fromDisk, $fileDirectory = null, $fileBaseName = null)
    {
        if (!$fromDisk->fileExists()) {
            throw new AppException(static::__transErrorWithModule('file_not_found'));
        }
        if (get_class($fromDisk) != static::class) {
            $fileRelativePath = $this->disk->putFileAs($this->fileAutoDirectory($fileDirectory), $fromDisk->getFile(), $fromDisk->fileAutoBaseName($fileBaseName));
            $this->file = new File(FileHelper::concatPath($this->directory, $fileRelativePath));
            $fromDisk->fileDelete();
        }
        return $this;
    }

    public function fileMove($fileDirectory = null, $fileBaseName = null)
    {
        if (!$this->fileExists()) {
            throw new AppException(static::__transErrorWithModule('file_not_found'));
        }
        if (!empty($fileDirectory) || !empty($fileBaseName)) {
            $toFileRelativePath = FileHelper::concatPath($this->fileAutoDirectory($fileDirectory), $this->fileAutoBaseName($fileBaseName));
            $this->disk->move($this->getFileRelativePath(), $toFileRelativePath);
            $this->file = new File(FileHelper::concatPath($this->directory, $toFileRelativePath));
        }
        return $this;
    }

    public function fileDuplicate($fileDirectory = null, $fileBaseName = null)
    {
        if (!$this->fileExists()) {
            throw new AppException(static::__transErrorWithModule('file_not_found'));
        }
        if (!empty($fileDirectory) || !empty($fileBaseName)) {
            $toFileRelativePath = FileHelper::concatPath($this->fileAutoDirectory($fileDirectory), $this->fileAutoBaseName($fileBaseName));
            $this->disk->copy($this->getFileRelativePath(), $toFileRelativePath);
            $this->file = new File(FileHelper::concatPath($this->directory, $toFileRelativePath));
        }
        return $this;
    }
}
