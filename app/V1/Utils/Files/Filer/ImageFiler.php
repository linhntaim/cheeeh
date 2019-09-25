<?php

namespace App\V1\Utils\Files\Filer;

use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class ImageFiler extends Filer
{
    /**
     * @var Image
     */
    protected $image;

    public function __construct($file)
    {
        parent::__construct($file);

        $this->prepare();
    }

    public function prepare()
    {
        $this->image = ImageManagerStatic::make($this->getRealPath());
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param boolean $aspectRatio
     * @param boolean $upSize
     */
    public function resize($width, $height, $aspectRatio = true, $upSize = false)
    {
        $this->image->resize($width, $height, function ($constraint) use ($aspectRatio, $upSize) {
            if ($aspectRatio) {
                $constraint->aspectRatio();
            }
            if ($upSize) {
                $constraint->upsize();
            }
        });
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param integer|null $x
     * @param integer|null $y
     */
    public function crop($width, $height, $x = null, $y = null)
    {
        $this->image->crop($width, $height, $x, $y);
    }

    /**
     * @param float $angle
     * @param string $bgColor
     */
    public function rotate($angle, $bgColor = '#ffffff')
    {
        $this->image->rotate($angle, $bgColor);
    }

    public function save($quality = null)
    {
        $this->image->save(null, $quality);
    }

    public function delete()
    {
        $this->image->destroy();
        $this->image = null;

        parent::delete();
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $separator1
     * @param string $separator2
     * @return ImageFiler
     */
    public function createThumbnail($width, $height, $separator1 = '_', $separator2 = 'x')
    {
        $fileName = sprintf('%s%s%s%s%s',
            $this->getFileName(),
            $separator1,
            $width,
            $separator2,
            $height
        );
        $thumbnailFiler = $this->duplicate($this->getRealDirectory(), $fileName);
        if ($this->image->getWidth() > $width || $this->image->getHeight() > $height) {
            $thumbnailFiler->resize($width, $height);
            $thumbnailFiler->save();
        }
        return $thumbnailFiler;
    }

    public function getResponse()
    {
        return $this->image->response();
    }
}
