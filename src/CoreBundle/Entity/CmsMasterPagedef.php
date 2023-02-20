<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMasterPagedef {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldText
/** @var string - Description */
private string $description = '', 
    // TCMSFieldVarchar
/** @var string - Layout */
private string $layout = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot[] - Spots */
private \Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock[] - Theme blocks */
private \Doctrine\Common\Collections\Collection $pkgCmsThemeBlockMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - Action-Plugins */
private string $actionPluginList = '', 
    // TCMSFieldBoolean
/** @var bool - Restrict to certain portals only */
private bool $restrictToPortals = false, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - CMS module extension */
private \Doctrine\Common\Collections\Collection $cmsPortalMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - WYSIWYG CSS URL */
private string $wysiwygCssUrl = '', 
    // TCMSFieldPosition
/** @var int -  */
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


  
    // TCMSFieldVarchar
public function getLayout(): string
{
    return $this->layout;
}
public function setLayout(string $layout): self
{
    $this->layout = $layout;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsMasterPagedefSpotCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMasterPagedefSpotCollection;
}
public function setCmsMasterPagedefSpotCollection(\Doctrine\Common\Collections\Collection $cmsMasterPagedefSpotCollection): self
{
    $this->cmsMasterPagedefSpotCollection = $cmsMasterPagedefSpotCollection;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getPkgCmsThemeBlockMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgCmsThemeBlockMlt;
}
public function setPkgCmsThemeBlockMlt(\Doctrine\Common\Collections\Collection $pkgCmsThemeBlockMlt): self
{
    $this->pkgCmsThemeBlockMlt = $pkgCmsThemeBlockMlt;

    return $this;
}


  
    // TCMSFieldText
public function getActionPluginList(): string
{
    return $this->actionPluginList;
}
public function setActionPluginList(string $actionPluginList): self
{
    $this->actionPluginList = $actionPluginList;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRestrictToPortals(): bool
{
    return $this->restrictToPortals;
}
public function setRestrictToPortals(bool $restrictToPortals): self
{
    $this->restrictToPortals = $restrictToPortals;

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
public function getWysiwygCssUrl(): string
{
    return $this->wysiwygCssUrl;
}
public function setWysiwygCssUrl(string $wysiwygCssUrl): self
{
    $this->wysiwygCssUrl = $wysiwygCssUrl;

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


  
}
