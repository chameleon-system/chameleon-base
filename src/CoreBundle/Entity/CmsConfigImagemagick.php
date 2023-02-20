<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfigImagemagick {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfig|null - Configuration */
private \ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig = null,
/** @var null|string - Configuration */
private ?string $cmsConfigId = null
, 
    // TCMSFieldNumber
/** @var int - Is effective from this image size in pixel */
private int $fromImageSize = 0, 
    // TCMSFieldBoolean
/** @var bool - Force JPEG. This extends to PNG.  */
private bool $forceJpeg = false, 
    // TCMSFieldNumber
/** @var int - Quality */
private int $quality = 100, 
    // TCMSFieldBoolean
/** @var bool - Sharpen */
private bool $scharpen = false, 
    // TCMSFieldDecimal
/** @var float - Gamma correction */
private float $gamma = 1  ) {}

  public function getId(): ?string
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
    // TCMSFieldLookup
public function getCmsConfig(): \ChameleonSystem\CoreBundle\Entity\CmsConfig|null
{
    return $this->cmsConfig;
}
public function setCmsConfig(\ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig): self
{
    $this->cmsConfig = $cmsConfig;
    $this->cmsConfigId = $cmsConfig?->getId();

    return $this;
}
public function getCmsConfigId(): ?string
{
    return $this->cmsConfigId;
}
public function setCmsConfigId(?string $cmsConfigId): self
{
    $this->cmsConfigId = $cmsConfigId;
    // todo - load new id
    //$this->cmsConfigId = $?->getId();

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
public function getGamma(): float
{
    return $this->gamma;
}
public function setGamma(float $gamma): self
{
    $this->gamma = $gamma;

    return $this;
}


  
}
