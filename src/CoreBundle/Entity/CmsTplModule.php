<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplModule {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldText
/** @var string - Description */
private string $description = '', 
    // TCMSFieldSmallIconList
/** @var string - Icon */
private string $iconList = 'application.png', 
    // TCMSFieldVarchar
/** @var string - Icon font CSS class */
private string $iconFontCssClass = '', 
    // TCMSFieldText
/** @var string - View / mapper configuration */
private string $viewMapperConfig = '', 
    // TCMSFieldText
/** @var string - Mapper chain */
private string $mapperChain = '', 
    // TCMSFieldText
/** @var string - Translations of the views */
private string $viewMapping = '', 
    // TCMSFieldBoolean
/** @var bool - Enable revision management */
private bool $revisionManagementActive = false, 
    // TCMSFieldBoolean
/** @var bool - Module contents are copied */
private bool $isCopyAllowed = false, 
    // TCMSFieldBoolean
/** @var bool - Show in template engine */
private bool $showInTemplateEngine = true, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldBoolean
/** @var bool - Offer module to specific groups only */
private bool $isRestricted = false, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] - Allow for these groups */
private \Doctrine\Common\Collections\Collection $cmsUsergroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - Display in portal */
private \Doctrine\Common\Collections\Collection $cmsPortalMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Module name */
private string $name = '', 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf[] - Connected tables */
private \Doctrine\Common\Collections\Collection $cmsTblConfMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Class name / service ID */
private string $classname = ''  ) {}

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
    // TCMSFieldText
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

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
public function getIconFontCssClass(): string
{
    return $this->iconFontCssClass;
}
public function setIconFontCssClass(string $iconFontCssClass): self
{
    $this->iconFontCssClass = $iconFontCssClass;

    return $this;
}


  
    // TCMSFieldText
public function getViewMapperConfig(): string
{
    return $this->viewMapperConfig;
}
public function setViewMapperConfig(string $viewMapperConfig): self
{
    $this->viewMapperConfig = $viewMapperConfig;

    return $this;
}


  
    // TCMSFieldText
public function getMapperChain(): string
{
    return $this->mapperChain;
}
public function setMapperChain(string $mapperChain): self
{
    $this->mapperChain = $mapperChain;

    return $this;
}


  
    // TCMSFieldText
public function getViewMapping(): string
{
    return $this->viewMapping;
}
public function setViewMapping(string $viewMapping): self
{
    $this->viewMapping = $viewMapping;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRevisionManagementActive(): bool
{
    return $this->revisionManagementActive;
}
public function setRevisionManagementActive(bool $revisionManagementActive): self
{
    $this->revisionManagementActive = $revisionManagementActive;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsCopyAllowed(): bool
{
    return $this->isCopyAllowed;
}
public function setIsCopyAllowed(bool $isCopyAllowed): self
{
    $this->isCopyAllowed = $isCopyAllowed;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowInTemplateEngine(): bool
{
    return $this->showInTemplateEngine;
}
public function setShowInTemplateEngine(bool $showInTemplateEngine): self
{
    $this->showInTemplateEngine = $showInTemplateEngine;

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


  
    // TCMSFieldBoolean
public function isIsRestricted(): bool
{
    return $this->isRestricted;
}
public function setIsRestricted(bool $isRestricted): self
{
    $this->isRestricted = $isRestricted;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsUsergroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsUsergroupMlt;
}
public function setCmsUsergroupMlt(\Doctrine\Common\Collections\Collection $cmsUsergroupMlt): self
{
    $this->cmsUsergroupMlt = $cmsUsergroupMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsPortalMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalMlt;
}
public function setCmsPortalMlt(\Doctrine\Common\Collections\Collection $cmsPortalMlt): self
{
    $this->cmsPortalMlt = $cmsPortalMlt;

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


  
    // TCMSFieldLookupMultiselect
public function getCmsTblConfMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTblConfMlt;
}
public function setCmsTblConfMlt(\Doctrine\Common\Collections\Collection $cmsTblConfMlt): self
{
    $this->cmsTblConfMlt = $cmsTblConfMlt;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassname(): string
{
    return $this->classname;
}
public function setClassname(string $classname): self
{
    $this->classname = $classname;

    return $this;
}


  
}
