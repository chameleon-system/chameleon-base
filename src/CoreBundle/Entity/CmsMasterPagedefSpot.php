<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMasterPagedefSpot {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null - Belongs to the CMS page template */
private \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null $cmsMasterPagedef = null,
/** @var null|string - Belongs to the CMS page template */
private ?string $cmsMasterPagedefId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null - Belongs to theme block */
private \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null $pkgCmsThemeBlock = null,
/** @var null|string - Belongs to theme block */
private ?string $pkgCmsThemeBlockId = null
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
    // TCMSFieldBoolean
/** @var bool - Static */
private bool $static = true, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpotParameter[] - Parameter */
private \Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotParameterCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpotAccess[] - Spot restrictions */
private \Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotAccessCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getCmsMasterPagedef(): \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null
{
    return $this->cmsMasterPagedef;
}
public function setCmsMasterPagedef(\ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef|null $cmsMasterPagedef): self
{
    $this->cmsMasterPagedef = $cmsMasterPagedef;
    $this->cmsMasterPagedefId = $cmsMasterPagedef?->getId();

    return $this;
}
public function getCmsMasterPagedefId(): ?string
{
    return $this->cmsMasterPagedefId;
}
public function setCmsMasterPagedefId(?string $cmsMasterPagedefId): self
{
    $this->cmsMasterPagedefId = $cmsMasterPagedefId;
    // todo - load new id
    //$this->cmsMasterPagedefId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getPkgCmsThemeBlock(): \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null
{
    return $this->pkgCmsThemeBlock;
}
public function setPkgCmsThemeBlock(\ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock|null $pkgCmsThemeBlock): self
{
    $this->pkgCmsThemeBlock = $pkgCmsThemeBlock;
    $this->pkgCmsThemeBlockId = $pkgCmsThemeBlock?->getId();

    return $this;
}
public function getPkgCmsThemeBlockId(): ?string
{
    return $this->pkgCmsThemeBlockId;
}
public function setPkgCmsThemeBlockId(?string $pkgCmsThemeBlockId): self
{
    $this->pkgCmsThemeBlockId = $pkgCmsThemeBlockId;
    // todo - load new id
    //$this->pkgCmsThemeBlockId = $?->getId();

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


  
    // TCMSFieldBoolean
public function isStatic(): bool
{
    return $this->static;
}
public function setStatic(bool $static): self
{
    $this->static = $static;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsMasterPagedefSpotParameterCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMasterPagedefSpotParameterCollection;
}
public function setCmsMasterPagedefSpotParameterCollection(\Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotParameterCollection): self
{
    $this->cmsMasterPagedefSpotParameterCollection = $cmsMasterPagedefSpotParameterCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsMasterPagedefSpotAccessCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMasterPagedefSpotAccessCollection;
}
public function setCmsMasterPagedefSpotAccessCollection(\Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotAccessCollection): self
{
    $this->cmsMasterPagedefSpotAccessCollection = $cmsMasterPagedefSpotAccessCollection;

    return $this;
}


  
}
