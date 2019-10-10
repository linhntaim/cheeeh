<?php

namespace App\V1\Utils\Files;

use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Helper;
use App\V1\Utils\StringHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Filer
{
    const RANDOM_FILENAME_LENGTH = 32;
    const RANDOM_DIRECTORY_MAX_FIRST_FOLDERS = 1000;

    /**
     * @var Disk
     */
    protected $disk;

    protected $directory;
    protected $filename;

    public function __construct($disk = null, $directory = null, $filename = null)
    {
        $this->disk = $disk ?: new LocalDisk();
        empty($directory) ? $this->randomDirectory() : $this->specificDirectory($directory);
        empty($filename) ? $this->randomFilename() : $this->specificFilename($filename);
    }

    protected function specificDirectory($directory)
    {
        $this->directory = sprintf('s/%s', $directory);
    }

    protected function randomDirectory($maxFirstFolder = Filer::RANDOM_DIRECTORY_MAX_FIRST_FOLDERS)
    {
        $now = DateTimeHelper::syncNowObject();
        $hash = function ($value) {
            return dechex(intval(StringHelper::repeat(9, strlen($value))) - intval($value));
        };
        $this->directory = sprintf('r/%s', implode(DIRECTORY_SEPARATOR, [
            $hash($now->format('Ym')),
            $hash($now->format('dH')),
            $hash(Helper::currentUserId(rand(0, $maxFirstFolder - 1)) % $maxFirstFolder + 170),
        ]));
    }

    protected function specificFilename($filename)
    {
        $this->filename = $filename;
    }

    protected function randomFilename($length = Filer::RANDOM_FILENAME_LENGTH)
    {
        $this->filename = Str::random($length);
    }

    public function putFile(UploadedFile $uploadedFile)
    {
    }
}
