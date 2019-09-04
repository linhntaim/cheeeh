<?php

namespace App\V1\Utils\Files\FileWriter;

use App\V1\Exceptions\AppException;
use ZipArchive;

class ZipArchiveHandler extends ZipHandler
{
    protected function _open($zipFilePath)
    {
        $this->handler = new ZipArchive;
        if ($this->handler->open($zipFilePath, ZipArchive::OVERWRITE) !== true) {
            throw new AppException($this->__transErrorWithModule('cannot_opened'));
        }
    }

    protected function _add($filePath, $relativeFilePath = null)
    {
        $this->handler->addFile($filePath, $relativeFilePath);
    }

    protected function _close()
    {
        $this->handler->close();
    }
}
