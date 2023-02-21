<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;
use ChameleonSystem\CoreBundle\Entity\CmsTreeNode;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CmsTree {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsTree|null - Is subnode of */
private ?CmsTree $parent = null
, 
    // TCMSFieldVarchar
/** @var string - Nested set: left */
private string $lft = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Nested set: right */
private string $rgt = '', 
    // TCMSFieldVarchar
/** @var string - URL name */
private string $urlname = '', 
    // TCMSFieldVarchar
/** @var string - Position */
private string $entrySort = '', 
    // TCMSFieldVarchar
/** @var string - External link */
private string $link = '', 
    // TCMSFieldVarchar
/** @var string - Pages / layouts */
private string $cmsTplPagePrimaryLink = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Icon for navigation */
private ?CmsMedia $naviIconCmsMedia = null
, 
    // TCMSFieldLookup
/** @var CmsTplModuleInstance|null - Connect module to navigation */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Hotkeys */
private string $htmlAccessKey = '', 
    // TCMSFieldVarchar
/** @var string - CSS classes */
private string $cssClasses = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTreeNode> - Connected pages */
private Collection $cmsTreeNodeCollection = new ArrayCollection()
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
    // TCMSFieldLookup
public function getParent(): ?CmsTree
{
    return $this->parent;
}

public function setParent(?CmsTree $parent): self
{
    $this->parent = $parent;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLft(): string
{
    return $this->lft;
}
public function setLft(string $lft): self
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


  
    // TCMSFieldVarchar
public function getRgt(): string
{
    return $this->rgt;
}
public function setRgt(string $rgt): self
{
    $this->rgt = $rgt;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUrlname(): string
{
    return $this->urlname;
}
public function setUrlname(string $urlname): self
{
    $this->urlname = $urlname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getEntrySort(): string
{
    return $this->entrySort;
}
public function setEntrySort(string $entrySort): self
{
    $this->entrySort = $entrySort;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLink(): string
{
    return $this->link;
}
public function setLink(string $link): self
{
    $this->link = $link;

    return $this;
}


  
    // TCMSFieldVarchar
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
public function getNaviIconCmsMedia(): ?CmsMedia
{
    return $this->naviIconCmsMedia;
}

public function setNaviIconCmsMedia(?CmsMedia $naviIconCmsMedia): self
{
    $this->naviIconCmsMedia = $naviIconCmsMedia;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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
/**
* @return Collection<int, cmsTreeNode>
*/
public function getCmsTreeNodeCollection(): Collection
{
    return $this->cmsTreeNodeCollection;
}

public function addCmsTreeNodeCollection(cmsTreeNode $cmsTreeNode): self
{
    if (!$this->cmsTreeNodeCollection->contains($cmsTreeNode)) {
        $this->cmsTreeNodeCollection->add($cmsTreeNode);
        $cmsTreeNode->setCmsTree($this);
    }

    return $this;
}

public function removeCmsTreeNodeCollection(cmsTreeNode $cmsTreeNode): self
{
    if ($this->cmsTreeNodeCollection->removeElement($cmsTreeNode)) {
        // set the owning side to null (unless already changed)
        if ($cmsTreeNode->getCmsTree() === $this) {
            $cmsTreeNode->setCmsTree(null);
        }
    }

    return $this;
}


  
}
