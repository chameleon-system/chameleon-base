<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTblConf;
use ChameleonSystem\CoreBundle\Entity\CmsMenuCategory;

class CmsMenuItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldLookup
/** @var CmsTblConf|null - Target */
private ?CmsTblConf $tar = null
, 
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


  
    // TCMSFieldLookup
public function getTar(): ?CmsTblConf
{
    return $this->tar;
}

public function setTar(?CmsTblConf $tar): self
{
    $this->tar = $tar;

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
