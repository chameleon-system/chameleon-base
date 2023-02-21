<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsContentBox;
use ChameleonSystem\CoreBundle\Entity\CmsUsergroup;

class CmsModule {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Description */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - CMS abbreviation */
private string $uniquecmsname = '', 
    // TCMSFieldLookup
/** @var CmsContentBox|null - Show in category window */
private ?CmsContentBox $cmsContentBox = null
, 
    // TCMSFieldLookup
/** @var CmsUsergroup|null - Module belongs to group */
private ?CmsUsergroup $cmsUsergroup = null
, 
    // TCMSFieldVarchar
/** @var string - Module page configuration */
private string $module = '', 
    // TCMSFieldVarchar
/** @var string - URL parameter */
private string $parameter = '', 
    // TCMSFieldVarchar
/** @var string - Module type */
private string $moduleLocation = 'Core', 
    // TCMSFieldVarchar
/** @var string - Popup window width */
private string $width = '780', 
    // TCMSFieldVarchar
/** @var string - Popup window height */
private string $height = '650', 
    // TCMSFieldVarchar
/** @var string - Icon Font CSS class */
private string $iconFontCssClass = ''  ) {}

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
public function getUniquecmsname(): string
{
    return $this->uniquecmsname;
}
public function setUniquecmsname(string $uniquecmsname): self
{
    $this->uniquecmsname = $uniquecmsname;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsContentBox(): ?CmsContentBox
{
    return $this->cmsContentBox;
}

public function setCmsContentBox(?CmsContentBox $cmsContentBox): self
{
    $this->cmsContentBox = $cmsContentBox;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUsergroup(): ?CmsUsergroup
{
    return $this->cmsUsergroup;
}

public function setCmsUsergroup(?CmsUsergroup $cmsUsergroup): self
{
    $this->cmsUsergroup = $cmsUsergroup;

    return $this;
}


  
    // TCMSFieldVarchar
public function getModule(): string
{
    return $this->module;
}
public function setModule(string $module): self
{
    $this->module = $module;

    return $this;
}


  
    // TCMSFieldVarchar
public function getParameter(): string
{
    return $this->parameter;
}
public function setParameter(string $parameter): self
{
    $this->parameter = $parameter;

    return $this;
}


  
    // TCMSFieldVarchar
public function getModuleLocation(): string
{
    return $this->moduleLocation;
}
public function setModuleLocation(string $moduleLocation): self
{
    $this->moduleLocation = $moduleLocation;

    return $this;
}


  
    // TCMSFieldVarchar
public function getWidth(): string
{
    return $this->width;
}
public function setWidth(string $width): self
{
    $this->width = $width;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHeight(): string
{
    return $this->height;
}
public function setHeight(string $height): self
{
    $this->height = $height;

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


  
}
