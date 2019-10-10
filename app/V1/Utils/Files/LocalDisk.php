<?php

namespace App\V1\Utils\Files;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalDisk extends Disk
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $disk;

    public function __construct($diskName = 'local')
    {
        $this->disk = Storage::disk($diskName);
    }

    public function putIn($file)
    {
        if($file instanceof UploadedFile) {
            return $this->disk->putFile('', $file);
        }
        if($file instanceof )
    }
}
