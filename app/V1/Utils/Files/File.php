<?php

namespace App\V1\Utils\Files;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File as BaseFile;

class File extends BaseFile
{
    protected function getTargetFile($directory, $name = null)
    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new FileException(sprintf('Unable to write in the "%s" directory', $directory));
        }

        $target = rtrim($directory, '/\\') . \DIRECTORY_SEPARATOR . (null === $name ? $this->getBasename() : $this->getName($name));

        return new static($target, false);
    }

    public function copy($directory, $name)
    {
        $target = $this->getTargetFile($directory, $name);
        set_error_handler(function ($type, $msg) use (&$error) {
            $error = $msg;
        });
        $copied = copy($this->getPathname(), $target);
        restore_error_handler();
        if (!$copied) {
            throw new FileException(sprintf('Could not copy the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
        }
        @chmod($target, 0666 & ~umask());
        return $target;
    }
}
