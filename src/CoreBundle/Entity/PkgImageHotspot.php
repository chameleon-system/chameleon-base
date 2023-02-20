<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;
use ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class PkgImageHotspot {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - How long should an image be displayed (in seconds)? */
private string $autoSlideTime = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgImageHotspotItem> - Images */
private Collection $pkgImageHotspotItemCollection = new ArrayCollection()
  ) {}

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
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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


  
    // TCMSFieldVarchar
public function getAutoSlideTime(): string
{
    return $this->autoSlideTime;
}
public function setAutoSlideTime(string $autoSlideTime): self
{
    $this->autoSlideTime = $autoSlideTime;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgImageHotspotItem>
*/
public function getPkgImageHotspotItemCollection(): Collection
{
    return $this->pkgImageHotspotItemCollection;
}

public function addPkgImageHotspotItemCollection(pkgImageHotspotItem $pkgImageHotspotItem): self
{
    if (!$this->pkgImageHotspotItemCollection->contains($pkgImageHotspotItem)) {
        $this->pkgImageHotspotItemCollection->add($pkgImageHotspotItem);
        $pkgImageHotspotItem->setPkgImageHotspot($this);
    }

    return $this;
}

public function removePkgImageHotspotItemCollection(pkgImageHotspotItem $pkgImageHotspotItem): self
{
    if ($this->pkgImageHotspotItemCollection->removeElement($pkgImageHotspotItem)) {
        // set the owning side to null (unless already changed)
        if ($pkgImageHotspotItem->getPkgImageHotspot() === $this) {
            $pkgImageHotspotItem->setPkgImageHotspot(null);
        }
    }

    return $this;
}


  
}
