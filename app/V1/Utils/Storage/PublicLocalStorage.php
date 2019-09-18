<?php

namespace App\V1\Utils\Storage;

class PublicLocalStorage extends LocalStorage
{
    public function __construct($diskName = 'public', $maxFirstFolder = Storage::MAX_FIRST_FOLDER)
    {
        parent::__construct($diskName, $maxFirstFolder);
    }
}
