<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTblConf;
use ChameleonSystem\CoreBundle\Entity\CmsModule;
use ChameleonSystem\CoreBundle\Entity\CmsMenuCustomItem;
use ChameleonSystem\CoreBundle\Entity\CmsMenuCategory;

class CmsMenuItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldExtendedLookupMultiTable
/** @var CmsTblConf|CmsModule|CmsMenuCustomItem|null - Target */
private ?CmsTblConf|CmsModule|CmsMenuCustomItem $tar = null
,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Target */
private string $targetTableName = '', 
    // TCMSFieldVarchar
/** @var string - Icon font CSS class */
private string $iconFontCssClass = '', 
    // TCMSFieldLookup
/** @var CmsMenuCategory|null - CMS main menu category */
private ?CmsMenuCategory $cmsMenuCategory = null
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


  
    // TCMSFieldExtendedLookupMultiTable
public function getTar(): ?CmsTblConf|CmsModule|CmsMenuCustomItem
{
    return $this->tar;
}

public function setTar(?CmsTblConf|CmsModule|CmsMenuCustomItem $tar): self
{
    $this->tar = $tar;

    return $this;
}
// TCMSFieldExtendedLookupMultiTable
public function getTarget(): string
{
    return $this->targetTableName;
}
public function setTarget(string $targetTableName): self
{
    $this->targetTableName = $targetTableName;

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


  
    // TCMSFieldLookup
public function getCmsMenuCategory(): ?CmsMenuCategory
{
    return $this->cmsMenuCategory;
}

public function setCmsMenuCategory(?CmsMenuCategory $cmsMenuCategory): self
{
    $this->cmsMenuCategory = $cmsMenuCategory;

    return $this;
}


  
}
