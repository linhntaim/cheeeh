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

    protected $isOverridden;

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

    public function getIsOverridden()
    {
        return $this->isOverridden;
    }

    public function setIsOverridden($value = true)
    {
        return $this->isOverridden = $value;
    }

    /**
     * @param string|array|bool $toDirectory
     * @param string|array|null $name
     * @param bool $isRelative
     * @param bool $safe
     * @return bool
     */
    protected function movable(&$toDirectory, &$name, $isRelative, $safe)
    {
        $fileHelper = FileHelper::getInstance();
        $toDirectory = $toDirectory === false ?
            dirname($this->getRealPath())
            : $fileHelper->autoDirectory($toDirectory, $isRelative);
        $name = $fileHelper->autoFilename(is_array($name) ? $name : [
            'name' => $name,
            'extension' => $this->file->getExtension(),
        ]);
        $this->isOverridden = file_exists($fileHelper->concatPath($toDirectory, $name));
        return !$safe || !$this->isOverridden;
    }

    /**
     * @param string|array|bool $toDirectory
     * @param string|array|null $name
     * @param bool $isRelative
     * @param bool $safe
     * @return Filer
     */
    public function move($toDirectory, $name = null, $isRelative = false, $safe = false)
    {
        if (!$this->movable($toDirectory, $name, $isRelative, $safe)) {
            return $this;
        }

        $this->file = $this->file->move($toDirectory, $name);
        return $this;
    }

    /**
     * @param string|array|bool $toDirectory
     * @param string|array|null $name
     * @param bool $isRelative
     * @return Filer
     */
    public function safeMove($toDirectory, $name = null, $isRelative = false)
    {
        return $this->move($toDirectory, $name, $isRelative, true);
    }

    /**
     * @param string|array|bool $toDirectory
     * @param string|array|null $name
     * @param bool $isRelative
     * @param bool $safe
     * @return Filer
     */
    public function duplicate($toDirectory, $name = null, $isRelative = false, $safe = false)
    {
        if (!$this->movable($toDirectory, $name, $isRelative, $safe)) {
            return $this;
        }

        $thisClass = $this->__class();
        $thisObject = new $thisClass($this->file->copy($toDirectory, $name));
        $thisObject->setIsOverridden($this->isOverridden);
        return $thisObject;
    }

    /**
     * @param string|array|bool $toDirectory
     * @param string|array|null $name
     * @param bool $isRelative
     * @return Filer
     */
    public function safeDuplicate($toDirectory, $name = null, $isRelative = false)
    {
        return $this->duplicate($toDirectory, $name, $isRelative, true);
    }

    /**
     * @param string|array|null $name
     * @return Filer
     */
    public function store($name = null)
    {
        return $this->move(FileHelper::getInstance()->storePath(), $name);
    }

    public function delete()
    {
        $path = $this->getRealPath();
        if (file_exists($path)) unlink($path);
        $this->file = null;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getBaseName()
    {
        return $this->file->getBasename();
    }

    public function getSize()
    {
        return $this->file->getSize();
    }

    public function getExtension()
    {
        return $this->file->getExtension();
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
