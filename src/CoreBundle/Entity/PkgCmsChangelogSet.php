<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsChangelogSet {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUser|null - User who made the change */
private \ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser = null,
/** @var null|string - User who made the change */
private ?string $cmsUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null - The main table that was changed */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf = null,
/** @var null|string - The main table that was changed */
private ?string $cmsTblConfId = null
, 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null - Change date */
private \DateTime|null $modifyDate = null, 
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
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogItem[] - Changes */
private \Doctrine\Common\Collections\Collection $pkgCmsChangelogItemCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
    // TCMSFieldDateTimeNow
public function getModifyDate(): \DateTime|null
{
    return $this->modifyDate;
}
public function setModifyDate(\DateTime|null $modifyDate): self
{
    $this->modifyDate = $modifyDate;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUser(): \ChameleonSystem\CoreBundle\Entity\CmsUser|null
{
    return $this->cmsUser;
}
public function setCmsUser(\ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser): self
{
    $this->cmsUser = $cmsUser;
    $this->cmsUserId = $cmsUser?->getId();

    return $this;
}
public function getCmsUserId(): ?string
{
    return $this->cmsUserId;
}
public function setCmsUserId(?string $cmsUserId): self
{
    $this->cmsUserId = $cmsUserId;
    // todo - load new id
    //$this->cmsUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsTblConf(): \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null
{
    return $this->cmsTblConf;
}
public function setCmsTblConf(\ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf): self
{
    $this->cmsTblConf = $cmsTblConf;
    $this->cmsTblConfId = $cmsTblConf?->getId();

    return $this;
}
public function getCmsTblConfId(): ?string
{
    return $this->cmsTblConfId;
}
public function setCmsTblConfId(?string $cmsTblConfId): self
{
    $this->cmsTblConfId = $cmsTblConfId;
    // todo - load new id
    //$this->cmsTblConfId = $?->getId();

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
public function getPkgCmsChangelogItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgCmsChangelogItemCollection;
}
public function setPkgCmsChangelogItemCollection(\Doctrine\Common\Collections\Collection $pkgCmsChangelogItemCollection): self
{
    $this->pkgCmsChangelogItemCollection = $pkgCmsChangelogItemCollection;

    return $this;
}


  
}
