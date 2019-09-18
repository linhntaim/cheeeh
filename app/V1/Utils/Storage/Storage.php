<?php

namespace App\V1\Utils\Storage;

use App\V1\Exceptions\AppException;
use App\V1\Utils\ClassTrait;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Helper;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use SplFileInfo;

abstract class Storage
{
    use ClassTrait;

    const MAX_FIRST_FOLDER = 1000;

    protected $disk;
    protected $maxFirstFolder;

    public function __construct($diskName, $maxFirstFolder = Storage::MAX_FIRST_FOLDER)
    {
        $this->disk = \Illuminate\Support\Facades\Storage::disk($diskName);
        $this->maxFirstFolder = $maxFirstFolder;
    }

    public function getDirectory()
    {
        $seed = Helper::currentUserId(rand(0, $this->maxFirstFolder - 1)) % $this->maxFirstFolder;
        $salt = 170; // AA
        $first = dechex($seed + 170); // + AA
        $now = DateTimeHelper::syncNowObject();
        $ym = dechex(1000000 - intval($now->format('Ym')));
        return dechex($this->getDirectory() % $this->maxFirstFolder);
    }

    protected function checkFile($file)
    {
        if (is_string($file)) {
            if (!file_exists($file)) {
                throw new AppException(static::__transErrorWithModule('file_not_found'));
            }
            return new File($file);
        }
        if ($file instanceof File || $file instanceof UploadedFile) {
            return $file;
        }
        if ($file instanceof SplFileInfo) {
            return new File($file->getRealPath());
        }

        throw new AppException(static::__transErrorWithModule('file_not_supported'));
    }

    public function putFile($file)
    {
        $file = $this->checkFile($file);
        $this->disk->putFile($this->getFirstFolderName(), $file);
    }
}
