<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class PkgShopListfilter {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Title to be shown on top of the filter on the website */
private string $title = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopListfilterItem> - List filter entries */
private Collection $pkgShopListfilterItemCollection = new ArrayCollection()
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
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgShopListfilterItem>
*/
public function getPkgShopListfilterItemCollection(): Collection
{
    return $this->pkgShopListfilterItemCollection;
}

public function addPkgShopListfilterItemCollection(pkgShopListfilterItem $pkgShopListfilterItem): self
{
    if (!$this->pkgShopListfilterItemCollection->contains($pkgShopListfilterItem)) {
        $this->pkgShopListfilterItemCollection->add($pkgShopListfilterItem);
        $pkgShopListfilterItem->setPkgShopListfilter($this);
    }

    return $this;
}

public function removePkgShopListfilterItemCollection(pkgShopListfilterItem $pkgShopListfilterItem): self
{
    if ($this->pkgShopListfilterItemCollection->removeElement($pkgShopListfilterItem)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopListfilterItem->getPkgShopListfilter() === $this) {
            $pkgShopListfilterItem->setPkgShopListfilter(null);
        }
    }

    return $this;
}


  
}
