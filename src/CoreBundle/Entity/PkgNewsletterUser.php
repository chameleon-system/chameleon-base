<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;
use ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation;
use ChameleonSystem\CoreBundle\Entity\CmsPortal;

class PkgNewsletterUser {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var DataExtranetUser|null - Belongs to customer */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldVarchar
/** @var string - Email address */
private string $email = '', 
    // TCMSFieldLookup
/** @var DataExtranetSalutation|null - Write delete log */
private ?DataExtranetSalutation $dataExtranetSalutation = null
, 
    // TCMSFieldVarchar
/** @var string - First name */
private string $firstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $lastname = '', 
    // TCMSFieldVarchar
/** @var string - Company */
private string $company = '', 
    // TCMSFieldLookup
/** @var CmsPortal|null - Portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Confirmation code */
private string $optincode = '', 
    // TCMSFieldVarchar
/** @var string - Unsubscription code */
private string $optoutcode = ''  ) {}

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
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

    return $this;
}


  
    // TCMSFieldVarchar
public function getEmail(): string
{
    return $this->email;
}
public function setEmail(string $email): self
{
    $this->email = $email;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataExtranetSalutation(): ?DataExtranetSalutation
{
    return $this->dataExtranetSalutation;
}

public function setDataExtranetSalutation(?DataExtranetSalutation $dataExtranetSalutation): self
{
    $this->dataExtranetSalutation = $dataExtranetSalutation;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFirstname(): string
{
    return $this->firstname;
}
public function setFirstname(string $firstname): self
{
    $this->firstname = $firstname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLastname(): string
{
    return $this->lastname;
}
public function setLastname(string $lastname): self
{
    $this->lastname = $lastname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCompany(): string
{
    return $this->company;
}
public function setCompany(string $company): self
{
    $this->company = $company;

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
public function getOptincode(): string
{
    return $this->optincode;
}
public function setOptincode(string $optincode): self
{
    $this->optincode = $optincode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getOptoutcode(): string
{
    return $this->optoutcode;
}
public function setOptoutcode(string $optoutcode): self
{
    $this->optoutcode = $optoutcode;

    return $this;
}


  
}
