<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\CmsLanguage;

class CmsUser {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Login */
private string $login = '', 
    // TCMSFieldVarchar
/** @var string - Password */
private string $cryptedPw = '', 
    // TCMSFieldVarchar
/** @var string - First name */
private string $firstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Email address */
private string $email = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Image */
private ?CmsMedia $ima = null
, 
    // TCMSFieldVarchar
/** @var string - Company */
private string $company = '', 
    // TCMSFieldVarchar
/** @var string - Department */
private string $department = '', 
    // TCMSFieldVarchar
/** @var string - City */
private string $city = '', 
    // TCMSFieldVarchar
/** @var string - Telephone */
private string $tel = '', 
    // TCMSFieldVarchar
/** @var string - Fax */
private string $fax = '', 
    // TCMSFieldLookup
/** @var CmsLanguage|null - CMS language */
private ?CmsLanguage $cmsLanguage = null
, 
    // TCMSFieldVarchar
/** @var string - Alternative languages */
private string $languages = 'de', 
    // TCMSFieldVarchar
/** @var string - Current editing language */
private string $cmsCurrentEditLanguage = '', 
    // TCMSFieldVarchar
/** @var string - Maximum displayed tasks */
private string $taskShowCount = '5', 
    // TCMSFieldVarchar
/** @var string - Google User ID */
private string $googleId = ''  ) {}

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
public function getLogin(): string
{
    return $this->login;
}
public function setLogin(string $login): self
{
    $this->login = $login;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCryptedPw(): string
{
    return $this->cryptedPw;
}
public function setCryptedPw(string $cryptedPw): self
{
    $this->cryptedPw = $cryptedPw;

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
public function getIma(): ?CmsMedia
{
    return $this->ima;
}

public function setIma(?CmsMedia $ima): self
{
    $this->ima = $ima;

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


  
    // TCMSFieldVarchar
public function getDepartment(): string
{
    return $this->department;
}
public function setDepartment(string $department): self
{
    $this->department = $department;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCity(): string
{
    return $this->city;
}
public function setCity(string $city): self
{
    $this->city = $city;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTel(): string
{
    return $this->tel;
}
public function setTel(string $tel): self
{
    $this->tel = $tel;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFax(): string
{
    return $this->fax;
}
public function setFax(string $fax): self
{
    $this->fax = $fax;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsLanguage(): ?CmsLanguage
{
    return $this->cmsLanguage;
}

public function setCmsLanguage(?CmsLanguage $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLanguages(): string
{
    return $this->languages;
}
public function setLanguages(string $languages): self
{
    $this->languages = $languages;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCmsCurrentEditLanguage(): string
{
    return $this->cmsCurrentEditLanguage;
}
public function setCmsCurrentEditLanguage(string $cmsCurrentEditLanguage): self
{
    $this->cmsCurrentEditLanguage = $cmsCurrentEditLanguage;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTaskShowCount(): string
{
    return $this->taskShowCount;
}
public function setTaskShowCount(string $taskShowCount): self
{
    $this->taskShowCount = $taskShowCount;

    return $this;
}


  
    // TCMSFieldVarchar
public function getGoogleId(): string
{
    return $this->googleId;
}
public function setGoogleId(string $googleId): self
{
    $this->googleId = $googleId;

    return $this;
}


  
}
