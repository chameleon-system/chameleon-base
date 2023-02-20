<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;

class CmsPortalDomains {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsPortal|null - Portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Domain name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - SSL domain name */
private string $sslname = '', 
    // TCMSFieldVarchar
/** @var string - Google API key */
private string $googleApiKey = ''  ) {}

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
    // TCMSFieldLookupParentID
public function getCmsPortal(): ?CmsPortal
{
    return $this->cmsPortal;
}

public function setCmsPortal(?CmsPortal $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;

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
