<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsDocument;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;

class CmsDocumentSecurityHash {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsDocument|null -  */
private ?CmsDocument $cmsDocument = null
, 
    // TCMSFieldLookup
/** @var DataExtranetUser|null -  */
private ?DataExtranetUser $dataExtranetUser = null
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
public function getCmsDocument(): ?CmsDocument
{
    return $this->cmsDocument;
}

public function setCmsDocument(?CmsDocument $cmsDocument): self
{
    $this->cmsDocument = $cmsDocument;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

    return $this;
}


  
}
