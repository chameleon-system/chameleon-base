<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsUser;
use ChameleonSystem\CoreBundle\Entity\CmsTblConf;

class CmsLock {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Record ID */
private string $recordid = '', 
    // TCMSFieldLookup
/** @var CmsUser|null - Editor */
private ?CmsUser $cmsUser = null
, 
    // TCMSFieldLookup
/** @var CmsTblConf|null - Lock table */
private ?CmsTblConf $cmsTblConf = null
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


  
}
