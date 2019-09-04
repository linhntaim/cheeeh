<?php

namespace App\V1\Utils\Files\FileWriter;

use Chumper\Zipper\Zipper;
use App\V1\Exceptions\AppException;
use Exception;

class ZipperHandler extends ZipHandler
{
    protected function _open($zipFilePath)
    {
        $this->handler = new Zipper();
        try {
            $this->handler->make($zipFilePath);
        } catch (Exception $exception) {
            throw AppException::from($exception);
        }
    }

    protected function _add($filePath, $relativeFilePath = null)
    {
        $this->handler->add($filePath, $relativeFilePath);
    }

    protected function _close()
    {
        $this->handler->close();
    }
}
