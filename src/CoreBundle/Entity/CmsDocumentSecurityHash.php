<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsDocumentSecurityHash {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument|null -  */
private \ChameleonSystem\CoreBundle\Entity\CmsDocument|null $cmsDocument = null,
/** @var null|string -  */
private ?string $cmsDocumentId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null -  */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string -  */
private ?string $dataExtranetUserId = null
, 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null -  */
private \DateTime|null $publishdate = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null -  */
private \DateTime|null $enddate = null, 
    // TCMSFieldUID
/** @var string -  */
private string $token = ''  ) {}

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
public function getCmsDocument(): \ChameleonSystem\CoreBundle\Entity\CmsDocument|null
{
    return $this->cmsDocument;
}
public function setCmsDocument(\ChameleonSystem\CoreBundle\Entity\CmsDocument|null $cmsDocument): self
{
    $this->cmsDocument = $cmsDocument;
    $this->cmsDocumentId = $cmsDocument?->getId();

    return $this;
}
public function getCmsDocumentId(): ?string
{
    return $this->cmsDocumentId;
}
public function setCmsDocumentId(?string $cmsDocumentId): self
{
    $this->cmsDocumentId = $cmsDocumentId;
    // todo - load new id
    //$this->cmsDocumentId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getDataExtranetUser(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null
{
    return $this->dataExtranetUser;
}
public function setDataExtranetUser(\ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;
    $this->dataExtranetUserId = $dataExtranetUser?->getId();

    return $this;
}
public function getDataExtranetUserId(): ?string
{
    return $this->dataExtranetUserId;
}
public function setDataExtranetUserId(?string $dataExtranetUserId): self
{
    $this->dataExtranetUserId = $dataExtranetUserId;
    // todo - load new id
    //$this->dataExtranetUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldDateTimeNow
public function getPublishdate(): \DateTime|null
{
    return $this->publishdate;
}
public function setPublishdate(\DateTime|null $publishdate): self
{
    $this->publishdate = $publishdate;

    return $this;
}


  
    // TCMSFieldDateTime
public function getEnddate(): \DateTime|null
{
    return $this->enddate;
}
public function setEnddate(\DateTime|null $enddate): self
{
    $this->enddate = $enddate;

    return $this;
}


  
    // TCMSFieldUID
public function getToken(): string
{
    return $this->token;
}
public function setToken(string $token): self
{
    $this->token = $token;

    return $this;
}


  
}
