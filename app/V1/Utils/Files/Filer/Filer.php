<?php

namespace App\V1\Utils\Files\Filer;

use App\V1\Exceptions\AppException;
use App\V1\Utils\Files\File;
use App\V1\Utils\Files\FileHelper;
use App\V1\Utils\Files\RelativeFileContainer;
use Illuminate\Http\UploadedFile;
use SplFileInfo;

class Filer extends RelativeFileContainer
{
    /**
     * @var File
     */
    protected $file;

    /**
     * Filer constructor.
     * @param string|SplFileInfo $file
     * @throws AppException
     */
    public function __construct($file)
    {
        $this->file = $this->checkFile($file);
    }

    /**
     * @param string|SplFileInfo $file
     * @return File
     * @throws AppException
     */
    protected function checkFile($file)
    {
        if (is_string($file)) {
            if (!file_exists($file)) {
                throw new AppException($this->__transErrorWithModule('file_not_found'));
            }
            return new File($file);
        }
        if ($file instanceof File) {
            return $file;
        }
        if ($file instanceof UploadedFile) {
            return new File(FileHelper::getInstance()->toDefaultRealPath(
                $file->store('') // saved in storage/app directory (local disk), which is default path, return relative path
            ));
        }
        if ($file instanceof SplFileInfo) {
            return new File($file->getRealPath());
        }

        throw new AppException($this->__transErrorWithModule('file_not_found'));
    }

    /**
     * @param string|array|bool $toDirectory
     * @param string|array|null $name
     * @param bool $isRelative
     * @return Filer
     */
    public function move($toDirectory, $name = null, $isRelative = false)
    {
        $fileHelper = FileHelper::getInstance();
        $this->file = $this->file->move(
            $toDirectory === false ?
                dirname($this->getRealPath())
                : $fileHelper->autoDirectory($toDirectory, $isRelative),
            $fileHelper->autoFilename(is_array($name) ? $name : [
                'name' => $name,
                'extension' => $this->file->getExtension(),
            ])
        );
        return $this;
    }

    /**
     * @param string|array|bool $toDirectory
     * @param string|array|null $name
     * @param bool $isRelative
     * @return Filer
     */
    public function duplicate($toDirectory, $name = null, $isRelative = false)
    {
        $fileHelper = FileHelper::getInstance();
        $thisClass = $this->__class();
        return new $thisClass($this->file->copy(
            $toDirectory === false ?
                dirname($this->getRealPath())
                : $fileHelper->autoDirectory($toDirectory, $isRelative),
            $fileHelper->autoFilename(is_array($name) ? $name : [
                'name' => $name,
                'extension' => $this->file->getExtension(),
            ])
        ));
    }

    /**
     * @param string|array|null $name
     * @return Filer
     */
    public function store($name = null)
    {
        return $this->move(FileHelper::getInstance()->storePath(), $name);
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getSize()
    {
        return $this->file->getSize();
    }

    public function getRealPath()
    {
        return $this->file->getRealPath();
    }

    public function getResponse()
    {
        return response()->file($this->getRealPath());
    }
}
