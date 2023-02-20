<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout;

class PkgCmsThemeBlock {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Descriptive name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsMasterPagedefSpot> - Spots */
private Collection $cmsMasterPagedefSpotCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgCmsThemeBlockLayout> - Layouts */
private Collection $pkgCmsThemeBlockLayoutCollection = new ArrayCollection()
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
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsMasterPagedefSpot>
*/
public function getCmsMasterPagedefSpotCollection(): Collection
{
    return $this->cmsMasterPagedefSpotCollection;
}

public function addCmsMasterPagedefSpotCollection(cmsMasterPagedefSpot $cmsMasterPagedefSpot): self
{
    if (!$this->cmsMasterPagedefSpotCollection->contains($cmsMasterPagedefSpot)) {
        $this->cmsMasterPagedefSpotCollection->add($cmsMasterPagedefSpot);
        $cmsMasterPagedefSpot->setPkgCmsThemeBlock($this);
    }

    return $this;
}

public function removeCmsMasterPagedefSpotCollection(cmsMasterPagedefSpot $cmsMasterPagedefSpot): self
{
    if ($this->cmsMasterPagedefSpotCollection->removeElement($cmsMasterPagedefSpot)) {
        // set the owning side to null (unless already changed)
        if ($cmsMasterPagedefSpot->getPkgCmsThemeBlock() === $this) {
            $cmsMasterPagedefSpot->setPkgCmsThemeBlock(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgCmsThemeBlockLayout>
*/
public function getPkgCmsThemeBlockLayoutCollection(): Collection
{
    return $this->pkgCmsThemeBlockLayoutCollection;
}

public function addPkgCmsThemeBlockLayoutCollection(pkgCmsThemeBlockLayout $pkgCmsThemeBlockLayout): self
{
    if (!$this->pkgCmsThemeBlockLayoutCollection->contains($pkgCmsThemeBlockLayout)) {
        $this->pkgCmsThemeBlockLayoutCollection->add($pkgCmsThemeBlockLayout);
        $pkgCmsThemeBlockLayout->setPkgCmsThemeBlock($this);
    }

    return $this;
}

public function removePkgCmsThemeBlockLayoutCollection(pkgCmsThemeBlockLayout $pkgCmsThemeBlockLayout): self
{
    if ($this->pkgCmsThemeBlockLayoutCollection->removeElement($pkgCmsThemeBlockLayout)) {
        // set the owning side to null (unless already changed)
        if ($pkgCmsThemeBlockLayout->getPkgCmsThemeBlock() === $this) {
            $pkgCmsThemeBlockLayout->setPkgCmsThemeBlock(null);
        }
    }

    return $this;
}


  
}
