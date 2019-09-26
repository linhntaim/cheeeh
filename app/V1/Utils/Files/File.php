<?php

namespace App\V1\Utils\Files;

use App\V1\Exceptions\AppException;
use App\V1\Utils\ClassTrait;
use Illuminate\Http\File as BaseFile;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class File extends BaseFile
{
    use ClassTrait;

    /**
     * @param string|SplFileInfo $file
     * @param bool $strict
     * @return File
     * @throws AppException
     */
    public static function from($file, $strict = true)
    {
        if ($file instanceof File) {
            if ($strict && !file_exists($file->getRealPath())) {
                throw new AppException(static::__transErrorWithModule('file_not_found'));
            }
            return $file;
        }
        if (is_string($file)) {
            if (!file_exists($file)) {
                throw new AppException(static::__transErrorWithModule('file_not_found'));
            }
            return new File($file, $strict);
        }
        if ($file instanceof BaseFile || $file instanceof SplFileInfo) {
            return new File($file->getRealPath(), $strict);
        }

        throw new AppException(static::__transErrorWithModule('file_not_supported'));
    }

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

        return new File($target, false);
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
