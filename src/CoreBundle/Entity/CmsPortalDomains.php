<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsPortalDomains {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Portal */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - Language */
private ?string $cmsLanguageId = null
, 
    // TCMSFieldVarchar
/** @var string - Domain name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - SSL domain name */
private string $sslname = '', 
    // TCMSFieldUniqueMarker
/** @var bool - Primary domain of the portal */
private bool $isMasterDomain = false, 
    // TCMSFieldVarchar
/** @var string - Google API key */
private string $googleApiKey = ''  ) {}

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


  
    // TCMSFieldVarchar
public function getSslname(): string
{
    return $this->sslname;
}
public function setSslname(string $sslname): self
{
    $this->sslname = $sslname;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->cmsLanguage;
}
public function setCmsLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;
    $this->cmsLanguageId = $cmsLanguage?->getId();

    return $this;
}
public function getCmsLanguageId(): ?string
{
    return $this->cmsLanguageId;
}
public function setCmsLanguageId(?string $cmsLanguageId): self
{
    $this->cmsLanguageId = $cmsLanguageId;
    // todo - load new id
    //$this->cmsLanguageId = $?->getId();

    return $this;
}



  
    // TCMSFieldUniqueMarker
public function isIsMasterDomain(): bool
{
    return $this->isMasterDomain;
}
public function setIsMasterDomain(bool $isMasterDomain): self
{
    $this->isMasterDomain = $isMasterDomain;

    return $this;
}


  
    // TCMSFieldVarchar
public function getGoogleApiKey(): string
{
    return $this->googleApiKey;
}
public function setGoogleApiKey(string $googleApiKey): self
{
    $this->googleApiKey = $googleApiKey;

    return $this;
}


  
}
