<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTree {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree|null - Is subnode of */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $parent = null,
/** @var null|string - Is subnode of */
private ?string $parentId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Icon for navigation */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $naviIconCmsMedia = null,
/** @var null|string - Icon for navigation */
private ?string $naviIconCmsMediaId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Connect module to navigation */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Connect module to navigation */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldNumber
/** @var int - Nested set: left */
private int $lft = 0, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldNumber
/** @var int - Nested set: right */
private int $rgt = 0, 
    // TCMSFieldSEOURLTitle
/** @var string - URL name */
private string $urlname = '', 
    // TCMSFieldBoolean
/** @var bool - Hide */
private bool $hidden = false, 
    // TCMSFieldBoolean
/** @var bool - Show restricted page in navigation */
private bool $showExtranetPage = false, 
    // TCMSFieldNumber
/** @var int - Position */
private int $entrySort = 0, 
    // TCMSFieldURL
/** @var string - External link */
private string $link = '', 
    // TCMSFieldBoolean
/** @var bool - Open link in new window */
private bool $linkTarget = false, 
    // TCMSFieldTreePageAssignment
/** @var string - Pages / layouts */
private string $cmsTplPagePrimaryLink = '', 
    // TCMSFieldText
/** @var string - Navigation path cache */
private string $pathcache = '', 
    // TCMSFieldBoolean
/** @var bool - SEO: no follow */
private bool $seoNofollow = false, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPage[] - SEO: no follow - page exclusion list */
private \Doctrine\Common\Collections\Collection $cmsTplPageMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Hotkeys */
private string $htmlAccessKey = '', 
    // TCMSFieldVarchar
/** @var string - CSS classes */
private string $cssClasses = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTreeNode[] - Connected pages */
private \Doctrine\Common\Collections\Collection $cmsTreeNodeCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getParent(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->parent;
}
public function setParent(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $parent): self
{
    $this->parent = $parent;
    $this->parentId = $parent?->getId();

    return $this;
}
public function getParentId(): ?string
{
    return $this->parentId;
}
public function setParentId(?string $parentId): self
{
    $this->parentId = $parentId;
    // todo - load new id
    //$this->parentId = $?->getId();

    return $this;
}



  
    // TCMSFieldNumber
public function getLft(): int
{
    return $this->lft;
}
public function setLft(int $lft): self
{
    $this->lft = $lft;

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


  
    // TCMSFieldNumber
public function getRgt(): int
{
    return $this->rgt;
}
public function setRgt(int $rgt): self
{
    $this->rgt = $rgt;

    return $this;
}


  
    // TCMSFieldSEOURLTitle
public function getUrlname(): string
{
    return $this->urlname;
}
public function setUrlname(string $urlname): self
{
    $this->urlname = $urlname;

    return $this;
}


  
    // TCMSFieldBoolean
public function isHidden(): bool
{
    return $this->hidden;
}
public function setHidden(bool $hidden): self
{
    $this->hidden = $hidden;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowExtranetPage(): bool
{
    return $this->showExtranetPage;
}
public function setShowExtranetPage(bool $showExtranetPage): self
{
    $this->showExtranetPage = $showExtranetPage;

    return $this;
}


  
    // TCMSFieldNumber
public function getEntrySort(): int
{
    return $this->entrySort;
}
public function setEntrySort(int $entrySort): self
{
    $this->entrySort = $entrySort;

    return $this;
}


  
    // TCMSFieldURL
public function getLink(): string
{
    return $this->link;
}
public function setLink(string $link): self
{
    $this->link = $link;

    return $this;
}


  
    // TCMSFieldBoolean
public function isLinkTarget(): bool
{
    return $this->linkTarget;
}
public function setLinkTarget(bool $linkTarget): self
{
    $this->linkTarget = $linkTarget;

    return $this;
}


  
    // TCMSFieldTreePageAssignment
public function getCmsTplPagePrimaryLink(): string
{
    return $this->cmsTplPagePrimaryLink;
}
public function setCmsTplPagePrimaryLink(string $cmsTplPagePrimaryLink): self
{
    $this->cmsTplPagePrimaryLink = $cmsTplPagePrimaryLink;

    return $this;
}


  
    // TCMSFieldLookup
public function getNaviIconCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->naviIconCmsMedia;
}
public function setNaviIconCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $naviIconCmsMedia): self
{
    $this->naviIconCmsMedia = $naviIconCmsMedia;
    $this->naviIconCmsMediaId = $naviIconCmsMedia?->getId();

    return $this;
}
public function getNaviIconCmsMediaId(): ?string
{
    return $this->naviIconCmsMediaId;
}
public function setNaviIconCmsMediaId(?string $naviIconCmsMediaId): self
{
    $this->naviIconCmsMediaId = $naviIconCmsMediaId;
    // todo - load new id
    //$this->naviIconCmsMediaId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getPathcache(): string
{
    return $this->pathcache;
}
public function setPathcache(string $pathcache): self
{
    $this->pathcache = $pathcache;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isSeoNofollow(): bool
{
    return $this->seoNofollow;
}
public function setSeoNofollow(bool $seoNofollow): self
{
    $this->seoNofollow = $seoNofollow;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getCmsTplPageMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTplPageMlt;
}
public function setCmsTplPageMlt(\Doctrine\Common\Collections\Collection $cmsTplPageMlt): self
{
    $this->cmsTplPageMlt = $cmsTplPageMlt;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHtmlAccessKey(): string
{
    return $this->htmlAccessKey;
}
public function setHtmlAccessKey(string $htmlAccessKey): self
{
    $this->htmlAccessKey = $htmlAccessKey;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCssClasses(): string
{
    return $this->cssClasses;
}
public function setCssClasses(string $cssClasses): self
{
    $this->cssClasses = $cssClasses;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsTreeNodeCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTreeNodeCollection;
}
public function setCmsTreeNodeCollection(\Doctrine\Common\Collections\Collection $cmsTreeNodeCollection): self
{
    $this->cmsTreeNodeCollection = $cmsTreeNodeCollection;

    return $this;
}


  
}
