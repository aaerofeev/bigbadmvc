<?php

namespace Framework\Helpers;

use Framework\Exceptions\CoreException;

class Image
{
    protected $img;

    protected $img_copy;

    protected $quality = 90;

    protected $width;

    protected $height;

    protected $type;

    protected $folderMode = 0755;

    public function __construct($filename)
    {
        $image = $this->setDimensionsFromImage($filename);
        $image->draw($filename);
    }

    protected function initialiseCanvas($width, $height, $resource = 'img')
    {
        $this->width = $width;
        $this->height = $height;

        unset($this->$resource);

        $this->$resource = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($this->$resource, true);
        imagealphablending($this->$resource, false);
        imagefilledrectangle($this->$resource, 0, 0, $this->width, $this->height, imagecolorallocatealpha($this->$resource, 0, 0, 0, 127));
        imagealphablending($this->$resource, true);

        return $this;
    }

    protected function shadowCopy()
    {
        $this->initialiseCanvas($this->width, $this->height, 'img_copy');
        imagecopy($this->img_copy, $this->img, 0, 0, 0, 0, $this->width, $this->height);
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setDimensionsFromImage($file)
    {
        if ($info = $this->getImageInfo($file, false)) {
            $this->initialiseCanvas($info->width, $info->height);
            return $this;
        }

        throw new CoreException($file . ' is not readable!');
    }

    protected function getImageInfo($file, $returnResource = true)
    {
        if (is_readable($file))
        {
            list($width, $height, $type) = getimagesize($file);
            switch ($type) {
                case IMAGETYPE_GIF:
                    if ($returnResource) {
                        $img = imagecreatefromgif($file);
                    }
                    break;

                case IMAGETYPE_JPEG:
                    if ($returnResource) {
                        $img = imagecreatefromjpeg($file);
                    }
                    break;

                case IMAGETYPE_PNG:
                    if ($returnResource) {
                        $img = imagecreatefrompng($file);
                    }
                    break;

                default:
                    return false;
                    break;
            }

        } else {
            return false;
        }

        $info = new \stdClass();
        $info->type = $type;

        if ($this->type === null) {
            $this->type = $type;
        }

        $info->width = $width;
        $info->height = $height;
        if ($returnResource) {
            $info->resource = $img;
        }

        return $info;
    }

    public function resize($targetWidth, $targetHeight, $upscale = false)
    {
        $width = $this->width;
        $height = $this->height;
        $r = $width / $height;
        $x = 0;
        $y = 0;

        if ($targetWidth / $targetHeight > $r) {
            $newwidth = intval($targetHeight * $r);
            $newheight = $targetHeight;
        } else {
            $newheight = intval($targetWidth / $r);
            $newwidth = $targetWidth;
        }

        if ($upscale === false) {
            if ($newwidth > $width) {
                $newwidth = $width;
            }
            if ($newheight > $height) {
                $newheight = $height;
            }
        }

        $canvasWidth = $newwidth;
        $canvasHeight = $newheight;

        $tmp = $this->img;
        $this->initialiseCanvas($canvasWidth, $canvasHeight);
        imagecopyresampled($this->img, $tmp, 0, 0, $x, $y, $newwidth, $newheight, $width, $height);
        imagedestroy($tmp);
        $this->shadowCopy();
        return $this;
    }

    public function cleanup()
    {
        imagedestroy($this->img);
    }

    public function save($path)
    {
        $this->checkQuality();

        if (!is_writable(dirname($path))) {
            if (!mkdir(dirname($path), $this->folderMode, true)) {
                throw new CoreException(dirname($path) . ' is not writable and failed to create directory structure!');
            }
        }
        if (is_writable(dirname($path))) {
            switch ($this->type) {
                case IMAGETYPE_GIF:
                    imagegif($this->img, $path);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($this->img, $path, $this->quality);
                    break;
                default:
                    imagejpeg($this->img, $path, $this->quality);
                    break;
            }
        } else {
            throw new CoreException(dirname($path) . ' is not writable!');
        }
    }

    public function draw($file, $x = '50%', $y = '50%')
    {
        if ($info = $this->getImageInfo($file)) {
            $image = $info->resource;
            $width = $info->width;
            $height = $info->height;
            if (strpos($x, '%') === false && !is_numeric($x) && !in_array($x, array('left', 'center', 'right'))) {
                $x = '50%';
            }
            if (strpos($y, '%') === false && !is_numeric($y) && !in_array($y, array('top', 'center', 'bottom'))) {
                $y = '50%';
            }

            switch ($x) {
                case 'left':
                    $x = '0%';
                    break;
                case 'center':
                    $x = '50%';
                    break;
                case 'right':
                    $x = '100%';
                    break;
            }
            switch ($y) {
                case 'top':
                    $y = '0%';
                    break;
                case 'center':
                    $y = '50%';
                    break;
                case 'bottom':
                    $y = '100%';
                    break;
            }
            // Work out offset
            if (strpos($x, '%') > -1) {
                $x = str_replace('%', '', $x);
                $x = ceil(($this->width - $width) * ($x / 100));
            }
            if (strpos($y, '%') > -1) {
                $y = str_replace('%', '', $y);
                $y = ceil(($this->height - $height) * ($y / 100));
            }
            // Draw image
            imagecopyresampled(
                $this->img,
                $image,
                $x,
                $y,
                0,
                0,
                $width,
                $height,
                $width,
                $height
            );
            imagedestroy($image);
            $this->shadowCopy();
            return $this;
        }

        throw new CoreException($file . ' is not a valid image!');
    }

    public function checkQuality()
    {
        switch ($this->type) {
            case IMAGETYPE_PNG:
                if ($this->quality > 9) {
                    $this->quality = 9;
                }
                break;
        }
        return $this;
    }

    public function setFolderMode($mode = 0755)
    {
        $this->folderMode = $mode;
        return $this;
    }

    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    public function setOutput($type, $quality = null)
    {
        switch (strtolower($type)) {
            case 'gif':
                $this->type = IMAGETYPE_GIF;
                break;
            case 'jpg':
                $this->type = IMAGETYPE_JPEG;
                break;
            case 'png':
                $this->type = IMAGETYPE_PNG;
                break;
        }

        if ($quality !== null) {
            $this->setQuality($quality);
        }

        return $this;
    }
}