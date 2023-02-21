<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;

class DataExtranet {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Session lifetime (in seconds) */
private string $sessionlife = '3600', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $fpwdTitle = '', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $noaccessTitle = '', 
    // TCMSFieldLookup
/** @var CmsPortal|null - Portal configuration */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Name of the spot where an extranet module is available */
private string $extranetSpotName = '', 
    // TCMSFieldVarchar
/** @var string - Validity of the password change key (in hours) */
private string $passwordChangeKeyTimeValidity = '2'  ) {}

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
public function getSessionlife(): string
{
    return $this->sessionlife;
}
public function setSessionlife(string $sessionlife): self
{
    $this->sessionlife = $sessionlife;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFpwdTitle(): string
{
    return $this->fpwdTitle;
}
public function setFpwdTitle(string $fpwdTitle): self
{
    $this->fpwdTitle = $fpwdTitle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNoaccessTitle(): string
{
    return $this->noaccessTitle;
}
public function setNoaccessTitle(string $noaccessTitle): self
{
    $this->noaccessTitle = $noaccessTitle;

    return $this;
}


  
    // TCMSFieldLookup
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
public function getExtranetSpotName(): string
{
    return $this->extranetSpotName;
}
public function setExtranetSpotName(string $extranetSpotName): self
{
    $this->extranetSpotName = $extranetSpotName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPasswordChangeKeyTimeValidity(): string
{
    return $this->passwordChangeKeyTimeValidity;
}
public function setPasswordChangeKeyTimeValidity(string $passwordChangeKeyTimeValidity): self
{
    $this->passwordChangeKeyTimeValidity = $passwordChangeKeyTimeValidity;

    return $this;
}


  
}
