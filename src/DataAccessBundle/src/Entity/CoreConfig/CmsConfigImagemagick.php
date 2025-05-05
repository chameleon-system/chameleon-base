<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreConfig;

class CmsConfigImagemagick
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsConfig|null - Configuration */
        private ?CmsConfig $cmsConfig = null,
        // TCMSFieldNumber
        /** @var int - Is effective from this image size in pixel */
        private int $fromImageSize = 0,
        // TCMSFieldBoolean
        /** @var bool - Force JPEG. This extends to PNG. */
        private bool $forceJpeg = false,
        // TCMSFieldNumber
        /** @var int - Quality */
        private int $quality = 100,
        // TCMSFieldBoolean
        /** @var bool - Sharpen */
        private bool $scharpen = false,
        // TCMSFieldDecimal
        /** @var string - Gamma correction */
        private string $gamma = '1'
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

    // TCMSFieldLookupParentID
    public function getCmsConfig(): ?CmsConfig
    {
        return $this->cmsConfig;
    }

    public function setCmsConfig(?CmsConfig $cmsConfig): self
    {
        $this->cmsConfig = $cmsConfig;

        return $this;
    }

    // TCMSFieldNumber
    public function getFromImageSize(): int
    {
        return $this->fromImageSize;
    }

    public function setFromImageSize(int $fromImageSize): self
    {
        $this->fromImageSize = $fromImageSize;

        return $this;
    }

    // TCMSFieldBoolean
    public function isForceJpeg(): bool
    {
        return $this->forceJpeg;
    }

    public function setForceJpeg(bool $forceJpeg): self
    {
        $this->forceJpeg = $forceJpeg;

        return $this;
    }

    // TCMSFieldNumber
    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    // TCMSFieldBoolean
    public function isScharpen(): bool
    {
        return $this->scharpen;
    }

    public function setScharpen(bool $scharpen): self
    {
        $this->scharpen = $scharpen;

        return $this;
    }

    // TCMSFieldDecimal
    public function getGamma(): string
    {
        return $this->gamma;
    }

    public function setGamma(string $gamma): self
    {
        $this->gamma = $gamma;

        return $this;
    }
}
