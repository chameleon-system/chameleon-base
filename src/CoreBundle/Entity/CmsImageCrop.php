<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsImageCrop {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Image */
private ?string $cmsMediaId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsImageCropPreset|null - Preset */
private \ChameleonSystem\CoreBundle\Entity\CmsImageCropPreset|null $cmsImageCropPreset = null,
/** @var null|string - Preset */
private ?string $cmsImageCropPresetId = null
, 
    // TCMSFieldNumber
/** @var int - X position of crop */
private int $posX = 0, 
    // TCMSFieldNumber
/** @var int - Y position of crop */
private int $posY = 0, 
    // TCMSFieldNumber
/** @var int -  */
private int $width = 0, 
    // TCMSFieldNumber
/** @var int - Crop height */
private int $height = 0, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = ''  ) {}

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
public function getCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMedia;
}
public function setCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;
    $this->cmsMediaId = $cmsMedia?->getId();

    return $this;
}
public function getCmsMediaId(): ?string
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(?string $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;
    // todo - load new id
    //$this->cmsMediaId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsImageCropPreset(): \ChameleonSystem\CoreBundle\Entity\CmsImageCropPreset|null
{
    return $this->cmsImageCropPreset;
}
public function setCmsImageCropPreset(\ChameleonSystem\CoreBundle\Entity\CmsImageCropPreset|null $cmsImageCropPreset): self
{
    $this->cmsImageCropPreset = $cmsImageCropPreset;
    $this->cmsImageCropPresetId = $cmsImageCropPreset?->getId();

    return $this;
}
public function getCmsImageCropPresetId(): ?string
{
    return $this->cmsImageCropPresetId;
}
public function setCmsImageCropPresetId(?string $cmsImageCropPresetId): self
{
    $this->cmsImageCropPresetId = $cmsImageCropPresetId;
    // todo - load new id
    //$this->cmsImageCropPresetId = $?->getId();

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
