<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsUser;
use ChameleonSystem\CoreBundle\Entity\CmsTblConf;
use ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class PkgCmsChangelogSet {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsUser|null - User who made the change */
private ?CmsUser $cmsU = null
, 
    // TCMSFieldLookup
/** @var CmsTblConf|null - The main table that was changed */
private ?CmsTblConf $cmsTblC = null
, 
    // TCMSFieldVarchar
/** @var string - ID of the changed data record */
private string $modifiedId = '', 
    // TCMSFieldVarchar
/** @var string - Name of the changed data record */
private string $modifiedName = '', 
    // TCMSFieldVarchar
/** @var string - Type of change (INSERT, UPDATE, DELETE) */
private string $changeType = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgCmsChangelogItem> - Changes */
private Collection $pkgCmsChangelogItemCollection = new ArrayCollection()
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
public function getCmsU(): ?CmsUser
{
    return $this->cmsU;
}

public function setCmsU(?CmsUser $cmsU): self
{
    $this->cmsU = $cmsU;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTblC(): ?CmsTblConf
{
    return $this->cmsTblC;
}

public function setCmsTblC(?CmsTblConf $cmsTblC): self
{
    $this->cmsTblC = $cmsTblC;

    return $this;
}


  
    // TCMSFieldVarchar
public function getModifiedId(): string
{
    return $this->modifiedId;
}
public function setModifiedId(string $modifiedId): self
{
    $this->modifiedId = $modifiedId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getModifiedName(): string
{
    return $this->modifiedName;
}
public function setModifiedName(string $modifiedName): self
{
    $this->modifiedName = $modifiedName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getChangeType(): string
{
    return $this->changeType;
}
public function setChangeType(string $changeType): self
{
    $this->changeType = $changeType;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, PkgCmsChangelogItem>
*/
public function getPkgCmsChangelogItemCollection(): Collection
{
    return $this->pkgCmsChangelogItemCollection;
}

public function addPkgCmsChangelogItemCollection(PkgCmsChangelogItem $pkgCmsChangelogItem): self
{
    if (!$this->pkgCmsChangelogItemCollection->contains($pkgCmsChangelogItem)) {
        $this->pkgCmsChangelogItemCollection->add($pkgCmsChangelogItem);
        $pkgCmsChangelogItem->setPkgCmsChangelogSet($this);
    }

    return $this;
}

public function removePkgCmsChangelogItemCollection(PkgCmsChangelogItem $pkgCmsChangelogItem): self
{
    if ($this->pkgCmsChangelogItemCollection->removeElement($pkgCmsChangelogItem)) {
        // set the owning side to null (unless already changed)
        if ($pkgCmsChangelogItem->getPkgCmsChangelogSet() === $this) {
            $pkgCmsChangelogItem->setPkgCmsChangelogSet(null);
        }
    }

    return $this;
}


  
}
