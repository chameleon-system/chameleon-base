<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsUrlAlias {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUser|null - Created by */
private \ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser = null,
/** @var null|string - Created by */
private ?string $cmsUserId = null
, 
    // TCMSFieldVarchar
/** @var string - Name / notes */
private string $name = '', 
    // TCMSFieldURL
/** @var string - Source */
private string $sourceUrl = '', 
    // TCMSFieldBoolean
/** @var bool - Exact match of the source path */
private bool $exactMatch = true, 
    // TCMSFieldVarchar
/** @var string - Target */
private string $targetUrl = '', 
    // TCMSFieldText
/** @var string - Ignore these parameters */
private string $ignoreParameter = '', 
    // TCMSFieldText
/** @var string - Parameter mapping */
private string $parameterMapping = '', 
    // TCMSFieldCreatedTimestamp
/** @var \DateTime|null - Creation date */
private \DateTime|null $datecreated = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Expiry date */
private \DateTime|null $expirationDate = null, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = true  ) {}

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
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

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


  
    // TCMSFieldURL
public function getSourceUrl(): string
{
    return $this->sourceUrl;
}
public function setSourceUrl(string $sourceUrl): self
{
    $this->sourceUrl = $sourceUrl;

    return $this;
}


  
    // TCMSFieldBoolean
public function isExactMatch(): bool
{
    return $this->exactMatch;
}
public function setExactMatch(bool $exactMatch): self
{
    $this->exactMatch = $exactMatch;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTargetUrl(): string
{
    return $this->targetUrl;
}
public function setTargetUrl(string $targetUrl): self
{
    $this->targetUrl = $targetUrl;

    return $this;
}


  
    // TCMSFieldText
public function getIgnoreParameter(): string
{
    return $this->ignoreParameter;
}
public function setIgnoreParameter(string $ignoreParameter): self
{
    $this->ignoreParameter = $ignoreParameter;

    return $this;
}


  
    // TCMSFieldText
public function getParameterMapping(): string
{
    return $this->parameterMapping;
}
public function setParameterMapping(string $parameterMapping): self
{
    $this->parameterMapping = $parameterMapping;

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



  
    // TCMSFieldCreatedTimestamp
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

    return $this;
}


  
    // TCMSFieldDateTime
public function getExpirationDate(): \DateTime|null
{
    return $this->expirationDate;
}
public function setExpirationDate(\DateTime|null $expirationDate): self
{
    $this->expirationDate = $expirationDate;

    return $this;
}


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
}
