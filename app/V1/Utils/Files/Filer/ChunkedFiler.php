<?php

namespace App\V1\Utils\Files\Filer;

use App\V1\Utils\Files\Disk\LocalDisk;
use App\V1\Utils\Files\FileHelper;
use App\V1\Utils\StringHelper;

class ChunkedFiler extends Filer
{
    const CHUNK_FILE_NAME = 'chunk';
    const CHUNK_FOLDER_NAME = 'chunks';

    public static function generateFileId()
    {
        $fileId = StringHelper::uuid();
        $chunkDirectory = FileHelper::getInstance()->concatPath(static::CHUNK_FOLDER_NAME, $fileId);
        if ((new LocalDisk())->exists($chunkDirectory)) { // prevent duplicate
            return static::generateFileId();
        }
        return $fileId;
    }

    protected $fileId;
    protected $chunkDirectory;
    protected $joinedFilePath;
    protected $joined;

    public function __construct($file, $fileId)
    {
        parent::__construct($file);

        $this->fileId = $fileId;
        $this->chunkDirectory = FileHelper::getInstance()->concatPath(static::CHUNK_FOLDER_NAME, $fileId);
        $this->joinedFilePath = $this->chunkDirectory . DIRECTORY_SEPARATOR . static::CHUNK_FILE_NAME;
        $this->joined = false;
        $this->moveInLocal($this->chunkDirectory);
    }
}
