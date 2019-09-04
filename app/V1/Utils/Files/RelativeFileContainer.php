<?php

namespace App\V1\Utils\Files;

use App\V1\Utils\ClassTrait;

abstract class RelativeFileContainer
{
    use ClassTrait;

    public abstract function getRealPath();

    public function isRelative()
    {
        return FileHelper::getInstance()->isRelativePath($this->getRealPath());
    }

    public function getRealDirectory()
    {
        return dirname($this->getRealPath());
    }

    public function getRelativePath()
    {
        return FileHelper::getInstance()->toRelativePath($this->getRealPath());
    }

    public function getRelativeDirectory()
    {
        return FileHelper::getInstance()->toRelativePath($this->getRealDirectory());
    }

    public function getUrl()
    {
        return FileHelper::getInstance()->toUrl($this->getRelativePath());
    }

    public function getBaseUrl()
    {
        return FileHelper::getInstance()->toUrl($this->getRelativeDirectory());
    }
}
