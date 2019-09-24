<?php

namespace App\V1\Utils\Files\Storage;

class LocalStorage extends Storage
{
    public function __construct($diskName = 'local', $maxFirstFolder = Storage::MAX_FIRST_FOLDER)
    {
        parent::__construct($diskName, $maxFirstFolder);
    }
}
