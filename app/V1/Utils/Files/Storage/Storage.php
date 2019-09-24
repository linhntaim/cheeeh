<?php

namespace App\V1\Utils\Files\Storage;

use App\V1\Utils\ClassTrait;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Files\File;
use App\V1\Utils\Helper;
use App\V1\Utils\StringHelper;

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

    public function putFile($file, $directory = null)
    {
        $file = File::from($file);
        $this->disk->putFile(empty($directory) ? $this->getDirectory() : $directory, $file);
    }
}
