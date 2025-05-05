<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreMedia;

class CmsImageCrop
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldExtendedLookup
        /** @var CmsMedia|null - Image */
        private ?CmsMedia $cmsMedia = null,
        // TCMSFieldLookup
        /** @var CmsImageCropPreset|null - Preset */
        private ?CmsImageCropPreset $cmsImageCropPreset = null,
        // TCMSFieldNumber
        /** @var int - X position of crop */
        private int $posX = 0,
        // TCMSFieldNumber
        /** @var int - Y position of crop */
        private int $posY = 0,
        // TCMSFieldNumber
        /** @var int - */
        private int $width = 0,
        // TCMSFieldNumber
        /** @var int - Crop height */
        private int $height = 0,
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = ''
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldExtendedLookup
    public function getCmsMedia(): ?CmsMedia
    {
        return $this->cmsMedia;
    }

    public function setCmsMedia(?CmsMedia $cmsMedia): self
    {
        $this->cmsMedia = $cmsMedia;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsImageCropPreset(): ?CmsImageCropPreset
    {
        return $this->cmsImageCropPreset;
    }

    public function setCmsImageCropPreset(?CmsImageCropPreset $cmsImageCropPreset): self
    {
        $this->cmsImageCropPreset = $cmsImageCropPreset;

        return $this;
    }

    // TCMSFieldNumber
    public function getPosX(): int
    {
        return $this->posX;
    }

    public function setPosX(int $posX): self
    {
        $this->posX = $posX;

        return $this;
    }

    // TCMSFieldNumber
    public function getPosY(): int
    {
        return $this->posY;
    }

    public function setPosY(int $posY): self
    {
        $this->posY = $posY;

        return $this;
    }

    // TCMSFieldNumber
    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    // TCMSFieldNumber
    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
