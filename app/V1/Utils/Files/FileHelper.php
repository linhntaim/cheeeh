<?php

namespace App\V1\Utils\Files;

use App\V1\Utils\NumberFormatHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileHelper
{
    private static $fileSizeType = ['byte', 'bytes', 'KB', 'MB', 'GB'];

    public static function hasBackPath($path)
    {
        return Str::startsWith('../', $path)
            || Str::startsWith('..\\', $path)
            || Str::contains('/../', $path)
            || Str::contains('\\..\\', $path)
            || Str::endsWith('/..', $path)
            || Str::endsWith('\\..', $path);
    }

    public static function concatPath()
    {
        $paths = [];
        foreach (func_get_args() as $arg) {
            if (is_array($arg)) {
                array_push($paths, static::concatPath(...$arg));
            } else {
                if (!empty($arg)) {
                    $paths[] = trim($arg, '/\\');
                }
            }
        }
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    public static function concatUrl()
    {
        $urls = [];
        foreach (func_get_args() as $arg) {
            if (is_array($arg)) {
                array_push($urls, static::concatUrl(...$arg));
            } else {
                if (!empty($arg)) {
                    $urls[] = trim($arg, '/\\');
                }
            }
        }
        return implode('/', $urls);
    }

    public static function changeToUrl($path)
    {
        return str_replace('\\', '/', $path);
    }

    public static function changeToPath($path)
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    public static function randomFileBaseName($extension = null)
    {
        return sprintf('%s%s', Str::random(40), empty($extension) ? '' : '.' . $extension);
    }

    public static function removeFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function removeDirectory($directory, $recursive = true)
    {
        if (is_dir($directory)) {
            foreach (scandir($directory) as $item) {
                if ($item != '.' && $item != '..') {
                    $itemPath = $directory . DIRECTORY_SEPARATOR . $item;
                    if (is_file($itemPath)) unlink($itemPath);
                    else {
                        if ($recursive) {
                            static::removeDirectory($itemPath);
                        }
                    }
                }
            }
            rmdir($directory);
        }
    }

    /**
     * Returns the maximum size of an uploaded file as configured in php.ini.
     *
     * @return int The maximum size of an uploaded file in bytes
     */
    public static function maxUploadFileSize()
    {
        return UploadedFile::getMaxFilesize();
    }

    public static function asSize($fileSize, $typeIndex = 1)
    {
        if ($fileSize > 1024) {
            return static::asSize($fileSize / 1024, ++$typeIndex);
        }
        if ($typeIndex == 1 && $fileSize <= 1) {
            $typeIndex = 0;
        }

        return NumberFormatHelper::getInstance()->formatNumber($fileSize) . ' ' . static::$fileSizeType[$typeIndex];
    }
}
