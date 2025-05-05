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
     * @var string|null
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
     * @param string|null $id
     * @param int $posX
     * @param int $posY
     * @param int $width
     * @param int $height
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
     *
     * @return void
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
     * @return void
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
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function setTargetWidth($targetWidth)
    {
        $this->targetHeight = $targetWidth;
    }
}
