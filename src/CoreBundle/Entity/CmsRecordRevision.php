<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsRecordRevision {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRecordRevision|null - belongs to revision */
private \ChameleonSystem\CoreBundle\Entity\CmsRecordRevision|null $cmsRecordRevision = null,
/** @var null|string - belongs to revision */
private ?string $cmsRecordRevisionId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null - Table */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf = null,
/** @var null|string - Table */
private ?string $cmsTblConfId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUser|null - Editor */
private \ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser = null,
/** @var null|string - Editor */
private ?string $cmsUserId = null
, 
    // TCMSFieldVarchar
/** @var string - Record ID */
private string $recordid = '', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldText
/** @var string - Description */
private string $description = '', 
    // TCMSFieldNumber
/** @var int - Version number */
private int $revisionNr = 0, 
    // TCMSFieldTimestamp
/** @var \DateTime|null - Created on */
private \DateTime|null $createTimestamp = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Time of last activation */
private \DateTime|null $lastActiveTimestamp = null, 
    // TCMSFieldText
/** @var string - Serialized record */
private string $data = ''  ) {}

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
public function getCmsRecordRevision(): \ChameleonSystem\CoreBundle\Entity\CmsRecordRevision|null
{
    return $this->cmsRecordRevision;
}
public function setCmsRecordRevision(\ChameleonSystem\CoreBundle\Entity\CmsRecordRevision|null $cmsRecordRevision): self
{
    $this->cmsRecordRevision = $cmsRecordRevision;
    $this->cmsRecordRevisionId = $cmsRecordRevision?->getId();

    return $this;
}
public function getCmsRecordRevisionId(): ?string
{
    return $this->cmsRecordRevisionId;
}
public function setCmsRecordRevisionId(?string $cmsRecordRevisionId): self
{
    $this->cmsRecordRevisionId = $cmsRecordRevisionId;
    // todo - load new id
    //$this->cmsRecordRevisionId = $?->getId();

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
public function getRecordid(): string
{
    return $this->recordid;
}
public function setRecordid(string $recordid): self
{
    $this->recordid = $recordid;

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


  
    // TCMSFieldNumber
public function getRevisionNr(): int
{
    return $this->revisionNr;
}
public function setRevisionNr(int $revisionNr): self
{
    $this->revisionNr = $revisionNr;

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



  
    // TCMSFieldTimestamp
public function getCreateTimestamp(): \DateTime|null
{
    return $this->createTimestamp;
}
public function setCreateTimestamp(\DateTime|null $createTimestamp): self
{
    $this->createTimestamp = $createTimestamp;

    return $this;
}


  
    // TCMSFieldDateTime
public function getLastActiveTimestamp(): \DateTime|null
{
    return $this->lastActiveTimestamp;
}
public function setLastActiveTimestamp(\DateTime|null $lastActiveTimestamp): self
{
    $this->lastActiveTimestamp = $lastActiveTimestamp;

    return $this;
}


  
    // TCMSFieldText
public function getData(): string
{
    return $this->data;
}
public function setData(string $data): self
{
    $this->data = $data;

    return $this;
}


  
}
