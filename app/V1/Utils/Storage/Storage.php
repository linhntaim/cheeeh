<?php

namespace App\V1\Utils\Storage;

use App\V1\Exceptions\AppException;
use App\V1\Utils\ClassTrait;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Helper;
use App\V1\Utils\StringHelper;
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
        $this->disk->putFile($this->getDirectory(), $file);
    }
}
