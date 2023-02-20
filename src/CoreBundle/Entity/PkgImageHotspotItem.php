<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspotItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspot|null - Belongs to image hotspot */
private \ChameleonSystem\CoreBundle\Entity\PkgImageHotspot|null $pkgImageHotspot = null,
/** @var null|string - Belongs to image hotspot */
private ?string $pkgImageHotspotId = null
, 
    // TCMSFieldVarchar
/** @var string - Alternative text for image */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Image */
private ?string $cmsMediaId = null
,
// ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Field\TCMSFieldMediaWithImageCrop
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Image - cropped image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMediaIdCropped = null, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItemSpot[] - Hotspots and linked areas */
private \Doctrine\Common\Collections\Collection $pkgImageHotspotItemSpotCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItemMarker[] - Hotspots with image */
private \Doctrine\Common\Collections\Collection $pkgImageHotspotItemMarkerCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getPkgImageHotspot(): \ChameleonSystem\CoreBundle\Entity\PkgImageHotspot|null
{
    return $this->pkgImageHotspot;
}
public function setPkgImageHotspot(\ChameleonSystem\CoreBundle\Entity\PkgImageHotspot|null $pkgImageHotspot): self
{
    $this->pkgImageHotspot = $pkgImageHotspot;
    $this->pkgImageHotspotId = $pkgImageHotspot?->getId();

    return $this;
}
public function getPkgImageHotspotId(): ?string
{
    return $this->pkgImageHotspotId;
}
public function setPkgImageHotspotId(?string $pkgImageHotspotId): self
{
    $this->pkgImageHotspotId = $pkgImageHotspotId;
    // todo - load new id
    //$this->pkgImageHotspotId = $?->getId();

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


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

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


// ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Field\TCMSFieldMediaWithImageCrop
public function getCmsMediaIdCropped(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMediaIdCropped;
}
public function setCmsMediaIdCropped(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMediaIdCropped): self
{
    $this->cmsMediaIdCropped = $cmsMediaIdCropped;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgImageHotspotItemSpotCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgImageHotspotItemSpotCollection;
}
public function setPkgImageHotspotItemSpotCollection(\Doctrine\Common\Collections\Collection $pkgImageHotspotItemSpotCollection): self
{
    $this->pkgImageHotspotItemSpotCollection = $pkgImageHotspotItemSpotCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgImageHotspotItemMarkerCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgImageHotspotItemMarkerCollection;
}
public function setPkgImageHotspotItemMarkerCollection(\Doctrine\Common\Collections\Collection $pkgImageHotspotItemMarkerCollection): self
{
    $this->pkgImageHotspotItemMarkerCollection = $pkgImageHotspotItemMarkerCollection;

    return $this;
}


  
}
