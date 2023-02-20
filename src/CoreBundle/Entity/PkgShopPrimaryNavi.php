<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPrimaryNavi {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldExtendedLookupMultiTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree|\ChameleonSystem\CoreBundle\Entity\ShopCategory|null - Select navigation */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|\ChameleonSystem\CoreBundle\Entity\ShopCategory|null $target = null,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Select navigation */
private string $targetTable = '', 
    // TCMSFieldBoolean
/** @var bool - Replace submenu with shop main categories */
private bool $showRootCategoryTree = false, 
    // TCMSFieldVarchar
/** @var string - Individual CSS class */
private string $cssClass = ''  ) {}

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
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

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


  
    // TCMSFieldExtendedLookupMultiTable
public function getTarget(): \ChameleonSystem\CoreBundle\Entity\CmsTree|\ChameleonSystem\CoreBundle\Entity\ShopCategory|null
{
    return $this->target;
}
public function setTarget(\ChameleonSystem\CoreBundle\Entity\CmsTree|\ChameleonSystem\CoreBundle\Entity\ShopCategory|null $target): self
{
    $this->target = $target;

    return $this;
}

// TCMSFieldExtendedLookupMultiTable
public function getTargetTable(): string
{
    return $this->targetTable;
}
public function setTargetTable(string $targetTable): self
{
    $this->targetTable = $targetTable;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowRootCategoryTree(): bool
{
    return $this->showRootCategoryTree;
}
public function setShowRootCategoryTree(bool $showRootCategoryTree): self
{
    $this->showRootCategoryTree = $showRootCategoryTree;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCssClass(): string
{
    return $this->cssClass;
}
public function setCssClass(string $cssClass): self
{
    $this->cssClass = $cssClass;

    return $this;
}


  
}
