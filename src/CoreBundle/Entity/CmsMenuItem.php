<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMenuItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMenuCategory|null - CMS main menu category */
private \ChameleonSystem\CoreBundle\Entity\CmsMenuCategory|null $cmsMenuCategory = null,
/** @var null|string - CMS main menu category */
private ?string $cmsMenuCategoryId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldExtendedLookupMultiTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|\ChameleonSystem\CoreBundle\Entity\CmsModule|\ChameleonSystem\CoreBundle\Entity\CmsMenuCustomItem|null - Target */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|\ChameleonSystem\CoreBundle\Entity\CmsModule|\ChameleonSystem\CoreBundle\Entity\CmsMenuCustomItem|null $target = null,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Target */
private string $targetTable = '', 
    // TCMSFieldVarchar
/** @var string - Icon font CSS class */
private string $iconFontCssClass = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0  ) {}

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


  
    // TCMSFieldExtendedLookupMultiTable
public function getTarget(): \ChameleonSystem\CoreBundle\Entity\CmsTblConf|\ChameleonSystem\CoreBundle\Entity\CmsModule|\ChameleonSystem\CoreBundle\Entity\CmsMenuCustomItem|null
{
    return $this->target;
}
public function setTarget(\ChameleonSystem\CoreBundle\Entity\CmsTblConf|\ChameleonSystem\CoreBundle\Entity\CmsModule|\ChameleonSystem\CoreBundle\Entity\CmsMenuCustomItem|null $target): self
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


  
    // TCMSFieldVarchar
public function getIconFontCssClass(): string
{
    return $this->iconFontCssClass;
}
public function setIconFontCssClass(string $iconFontCssClass): self
{
    $this->iconFontCssClass = $iconFontCssClass;

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
public function getCmsMenuCategory(): \ChameleonSystem\CoreBundle\Entity\CmsMenuCategory|null
{
    return $this->cmsMenuCategory;
}
public function setCmsMenuCategory(\ChameleonSystem\CoreBundle\Entity\CmsMenuCategory|null $cmsMenuCategory): self
{
    $this->cmsMenuCategory = $cmsMenuCategory;
    $this->cmsMenuCategoryId = $cmsMenuCategory?->getId();

    return $this;
}
public function getCmsMenuCategoryId(): ?string
{
    return $this->cmsMenuCategoryId;
}
public function setCmsMenuCategoryId(?string $cmsMenuCategoryId): self
{
    $this->cmsMenuCategoryId = $cmsMenuCategoryId;
    // todo - load new id
    //$this->cmsMenuCategoryId = $?->getId();

    return $this;
}



  
}
