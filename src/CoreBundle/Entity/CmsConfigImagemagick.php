<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsConfig;

class CmsConfigImagemagick {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsConfig|null - Configuration */
private ?CmsConfig $cmsConfig = null
, 
    // TCMSFieldVarchar
/** @var string - Is effective from this image size in pixel */
private string $fromImageSize = '', 
    // TCMSFieldVarchar
/** @var string - Quality */
private string $quality = '100'  ) {}

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


  
    // TCMSFieldVarchar
public function getFromImageSize(): string
{
    return $this->fromImageSize;
}
public function setFromImageSize(string $fromImageSize): self
{
    $this->fromImageSize = $fromImageSize;

    return $this;
}


  
    // TCMSFieldVarchar
public function getQuality(): string
{
    return $this->quality;
}
public function setQuality(string $quality): self
{
    $this->quality = $quality;

    return $this;
}


  
}
