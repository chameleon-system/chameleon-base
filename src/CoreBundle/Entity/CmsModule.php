<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsModule {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsContentBox|null - Show in category window */
private \ChameleonSystem\CoreBundle\Entity\CmsContentBox|null $cmsContentBox = null,
/** @var null|string - Show in category window */
private ?string $cmsContentBoxId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null - Module belongs to group */
private \ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null $cmsUsergroup = null,
/** @var null|string - Module belongs to group */
private ?string $cmsUsergroupId = null
, 
    // TCMSFieldSmallIconList
/** @var string - Icon */
private string $iconList = 'page_package.gif', 
    // TCMSFieldVarchar
/** @var string - Description */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - CMS abbreviation */
private string $uniquecmsname = '', 
    // TCMSFieldVarchar
/** @var string - Module page configuration */
private string $module = '', 
    // TCMSFieldVarchar
/** @var string - URL parameter */
private string $parameter = '', 
    // TCMSFieldVarchar
/** @var string - Module type */
private string $moduleLocation = 'Core', 
    // TCMSFieldBoolean
/** @var bool - Open as popup */
private bool $showAsPopup = false, 
    // TCMSFieldNumber
/** @var int - Popup window width */
private int $width = 780, 
    // TCMSFieldNumber
/** @var int - Popup window height */
private int $height = 650, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldVarchar
/** @var string - Icon Font CSS class */
private string $iconFontCssClass = ''  ) {}

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
    // TCMSFieldSmallIconList
public function getIconList(): string
{
    return $this->iconList;
}
public function setIconList(string $iconList): self
{
    $this->iconList = $iconList;

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
public function getCmsContentBox(): \ChameleonSystem\CoreBundle\Entity\CmsContentBox|null
{
    return $this->cmsContentBox;
}
public function setCmsContentBox(\ChameleonSystem\CoreBundle\Entity\CmsContentBox|null $cmsContentBox): self
{
    $this->cmsContentBox = $cmsContentBox;
    $this->cmsContentBoxId = $cmsContentBox?->getId();

    return $this;
}
public function getCmsContentBoxId(): ?string
{
    return $this->cmsContentBoxId;
}
public function setCmsContentBoxId(?string $cmsContentBoxId): self
{
    $this->cmsContentBoxId = $cmsContentBoxId;
    // todo - load new id
    //$this->cmsContentBoxId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsUsergroup(): \ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null
{
    return $this->cmsUsergroup;
}
public function setCmsUsergroup(\ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null $cmsUsergroup): self
{
    $this->cmsUsergroup = $cmsUsergroup;
    $this->cmsUsergroupId = $cmsUsergroup?->getId();

    return $this;
}
public function getCmsUsergroupId(): ?string
{
    return $this->cmsUsergroupId;
}
public function setCmsUsergroupId(?string $cmsUsergroupId): self
{
    $this->cmsUsergroupId = $cmsUsergroupId;
    // todo - load new id
    //$this->cmsUsergroupId = $?->getId();

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


  
    // TCMSFieldBoolean
public function isShowAsPopup(): bool
{
    return $this->showAsPopup;
}
public function setShowAsPopup(bool $showAsPopup): self
{
    $this->showAsPopup = $showAsPopup;

    return $this;
}


  
    // TCMSFieldNumber
public function getWidth(): int
{
    return $this->width;
}
public function setWidth(int $width): self
{
    $this->width = $width;

    return $this;
}


  
    // TCMSFieldNumber
public function getHeight(): int
{
    return $this->height;
}
public function setHeight(int $height): self
{
    $this->height = $height;

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
