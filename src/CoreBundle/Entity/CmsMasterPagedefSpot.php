<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef;
use ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock;
use ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpotParameter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpotAccess;

class CmsMasterPagedefSpot {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsMasterPagedef|null - Belongs to the CMS page template */
private ?CmsMasterPagedef $cmsMasterPagedef = null
, 
    // TCMSFieldLookup
/** @var PkgCmsThemeBlock|null - Belongs to theme block */
private ?PkgCmsThemeBlock $pkgCmsThemeBlock = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Model (class name) */
private string $model = '', 
    // TCMSFieldVarchar
/** @var string - Module view */
private string $view = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsMasterPagedefSpotParameter> - Parameter */
private Collection $cmsMasterPagedefSpotParameterCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsMasterPagedefSpotAccess> - Spot restrictions */
private Collection $cmsMasterPagedefSpotAccessCollection = new ArrayCollection()
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
    // TCMSFieldLookup
public function getCmsMasterPagedef(): ?CmsMasterPagedef
{
    return $this->cmsMasterPagedef;
}

public function setCmsMasterPagedef(?CmsMasterPagedef $cmsMasterPagedef): self
{
    $this->cmsMasterPagedef = $cmsMasterPagedef;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgCmsThemeBlock(): ?PkgCmsThemeBlock
{
    return $this->pkgCmsThemeBlock;
}

public function setPkgCmsThemeBlock(?PkgCmsThemeBlock $pkgCmsThemeBlock): self
{
    $this->pkgCmsThemeBlock = $pkgCmsThemeBlock;

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
public function getModel(): string
{
    return $this->model;
}
public function setModel(string $model): self
{
    $this->model = $model;

    return $this;
}


  
    // TCMSFieldVarchar
public function getView(): string
{
    return $this->view;
}
public function setView(string $view): self
{
    $this->view = $view;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsMasterPagedefSpotParameter>
*/
public function getCmsMasterPagedefSpotParameterCollection(): Collection
{
    return $this->cmsMasterPagedefSpotParameterCollection;
}

public function addCmsMasterPagedefSpotParameterCollection(cmsMasterPagedefSpotParameter $cmsMasterPagedefSpotParameter): self
{
    if (!$this->cmsMasterPagedefSpotParameterCollection->contains($cmsMasterPagedefSpotParameter)) {
        $this->cmsMasterPagedefSpotParameterCollection->add($cmsMasterPagedefSpotParameter);
        $cmsMasterPagedefSpotParameter->setCmsMasterPagedefSpot($this);
    }

    return $this;
}

public function removeCmsMasterPagedefSpotParameterCollection(cmsMasterPagedefSpotParameter $cmsMasterPagedefSpotParameter): self
{
    if ($this->cmsMasterPagedefSpotParameterCollection->removeElement($cmsMasterPagedefSpotParameter)) {
        // set the owning side to null (unless already changed)
        if ($cmsMasterPagedefSpotParameter->getCmsMasterPagedefSpot() === $this) {
            $cmsMasterPagedefSpotParameter->setCmsMasterPagedefSpot(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsMasterPagedefSpotAccess>
*/
public function getCmsMasterPagedefSpotAccessCollection(): Collection
{
    return $this->cmsMasterPagedefSpotAccessCollection;
}

public function addCmsMasterPagedefSpotAccessCollection(cmsMasterPagedefSpotAccess $cmsMasterPagedefSpotAccess): self
{
    if (!$this->cmsMasterPagedefSpotAccessCollection->contains($cmsMasterPagedefSpotAccess)) {
        $this->cmsMasterPagedefSpotAccessCollection->add($cmsMasterPagedefSpotAccess);
        $cmsMasterPagedefSpotAccess->setCmsMasterPagedefSpot($this);
    }

    return $this;
}

public function removeCmsMasterPagedefSpotAccessCollection(cmsMasterPagedefSpotAccess $cmsMasterPagedefSpotAccess): self
{
    if ($this->cmsMasterPagedefSpotAccessCollection->removeElement($cmsMasterPagedefSpotAccess)) {
        // set the owning side to null (unless already changed)
        if ($cmsMasterPagedefSpotAccess->getCmsMasterPagedefSpot() === $this) {
            $cmsMasterPagedefSpotAccess->setCmsMasterPagedefSpot(null);
        }
    }

    return $this;
}


  
}
