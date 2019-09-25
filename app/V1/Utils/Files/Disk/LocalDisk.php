<?php

namespace App\V1\Utils\Files\Disk;

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

    protected function getDirectory()
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

    public function handle($file)
    {
        if ($file instanceof UploadedFile) {
            $relativeFilePath = $this->disk->putFile('', $file);
            $this->file = new File(FileHelper::getInstance()->concatPath(
                $this->directory, $relativeFilePath
            ));
            return $this;
        }
        return parent::handle($file);
    }

    public function in($path)
    {
        return Str::startsWith($path, $this->directory);
    }

    public function exists($path)
    {
        return $this->disk->exists($path);
    }

    public function moveIn(Disk $fromDisk)
    {
        $relativeFilePath = $this->disk->putFile($this->getDirectory(), $fromDisk->getFile());
        $this->file = new File(FileHelper::getInstance()->concatPath(
            $this->directory, $relativeFilePath
        ));
        return $this;
    }

    public function move($directory = null, $name = null)
    {
        $relativeFilePath = $this->disk->putFile($directory ?: $this->getDirectory(), $this->file);
        $this->file = new File(FileHelper::getInstance()->concatPath(
            $this->directory, $relativeFilePath
        ));
    }
}
