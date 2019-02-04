<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCrop\DataModel;

class ImageCropDataModel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var CmsMediaDataModel
     */
    private $cmsMedia;

    /**
     * @var ImageCropPresetDataModel|null
     */
    private $imageCropPreset;

    /**
     * @var int
     */
    private $posX;

    /**
     * @var int
     */
    private $posY;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var int|null
     */
    private $targetWidth;

    /**
     * @var int|null
     */
    private $targetHeight;

    /**
     * @param string|null       $id
     * @param CmsMediaDataModel $cmsMedia
     * @param int               $posX
     * @param int               $posY
     * @param int               $width
     * @param int               $height
     */
    public function __construct(
        $id,
        CmsMediaDataModel $cmsMedia,
        $posX,
        $posY,
        $width,
        $height
    ) {
        $this->id = $id;
        $this->cmsMedia = $cmsMedia;
        $this->posX = $posX;
        $this->posY = $posY;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return CmsMediaDataModel
     */
    public function getCmsMedia()
    {
        return $this->cmsMedia;
    }

    /**
     * @param CmsMediaDataModel $cmsMedia
     */
    public function setCmsMedia(CmsMediaDataModel $cmsMedia)
    {
        $this->cmsMedia = $cmsMedia;
    }

    /**
     * @return ImageCropPresetDataModel|null
     */
    public function getImageCropPreset()
    {
        return $this->imageCropPreset;
    }

    /**
     * @param ImageCropPresetDataModel|null $imageCropPreset
     */
    public function setImageCropPreset(ImageCropPresetDataModel $imageCropPreset)
    {
        $this->imageCropPreset = $imageCropPreset;
    }

    /**
     * @return int
     */
    public function getPosX()
    {
        return $this->posX;
    }

    /**
     * @param int $posX
     */
    public function setPosX($posX)
    {
        $this->posX = $posX;
    }

    /**
     * @return int
     */
    public function getPosY()
    {
        return $this->posY;
    }

    /**
     * @param int $posY
     */
    public function setPosY($posY)
    {
        $this->posY = $posY;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getTargetHeight()
    {
        return $this->targetHeight;
    }

    /**
     * @param int|null $targetHeight
     */
    public function setTargetHeight($targetHeight)
    {
        $this->targetHeight = $targetHeight;
    }

    /**
     * @return int|null
     */
    public function getTargetWidth()
    {
        return $this->targetWidth;
    }

    /**
     * @param int|null $targetWidth
     */
    public function setTargetWidth($targetWidth)
    {
        $this->targetHeight = $targetWidth;
    }
}
