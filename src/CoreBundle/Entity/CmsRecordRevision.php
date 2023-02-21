<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTblConf;
use ChameleonSystem\CoreBundle\Entity\CmsUser;

class CmsRecordRevision {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsRecordRevision|null - belongs to revision */
private ?CmsRecordRevision $cmsRecordRevision = null
, 
    // TCMSFieldLookup
/** @var CmsTblConf|null - Table */
private ?CmsTblConf $cmsTblConf = null
, 
    // TCMSFieldVarchar
/** @var string - Record ID */
private string $recordid = '', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Version number */
private string $revisionNr = '', 
    // TCMSFieldLookup
/** @var CmsUser|null - Editor */
private ?CmsUser $cmsUser = null
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
public function getCmsRecordRevision(): ?CmsRecordRevision
{
    return $this->cmsRecordRevision;
}

public function setCmsRecordRevision(?CmsRecordRevision $cmsRecordRevision): self
{
    $this->cmsRecordRevision = $cmsRecordRevision;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTblConf(): ?CmsTblConf
{
    return $this->cmsTblConf;
}

public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
{
    $this->cmsTblConf = $cmsTblConf;

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


  
    // TCMSFieldVarchar
public function getRevisionNr(): string
{
    return $this->revisionNr;
}
public function setRevisionNr(string $revisionNr): self
{
    $this->revisionNr = $revisionNr;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUser(): ?CmsUser
{
    return $this->cmsUser;
}

public function setCmsUser(?CmsUser $cmsUser): self
{
    $this->cmsUser = $cmsUser;

    return $this;
}


  
}
