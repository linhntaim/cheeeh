<?php

namespace App\V1\Utils\Files\Disk;

class PublicDisk extends LocalDisk
{
    public function __construct($diskName = 'public', $maxFirstFolder = PublicDisk::MAX_FIRST_FOLDER)
    {
        parent::__construct($diskName, $maxFirstFolder);
    }
}
