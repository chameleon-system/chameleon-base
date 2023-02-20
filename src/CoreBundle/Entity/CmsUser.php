<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsUser {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $images = null,
/** @var null|string - Image */
private ?string $imagesId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - CMS language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - CMS language */
private ?string $cmsLanguageId = null
, 
    // TCMSFieldVarchar
/** @var string - Login */
private string $login = '', 
    // TCMSFieldPasswordEncrypted
/** @var string - Password */
private string $cryptedPw = '', 
    // TCMSFieldVarchar
/** @var string - First name */
private string $firstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $name = '', 
    // TCMSFieldEmail
/** @var string - Email address */
private string $email = '', 
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
    // TCMSFieldVarchar
/** @var string - Alternative languages */
private string $languages = 'de', 
    // TCMSFieldLookupGroups
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] - User groups */
private \Doctrine\Common\Collections\Collection $cmsUsergroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupRoles
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - User roles */
private \Doctrine\Common\Collections\Collection $cmsRoleMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - Portal / websites */
private \Doctrine\Common\Collections\Collection $cmsPortalMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage[] - Editing languages */
private \Doctrine\Common\Collections\Collection $cmsLanguageMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Current editing language */
private string $cmsCurrentEditLanguage = '', 
    // TCMSFieldBoolean
/** @var bool - Allow CMS login */
private bool $allowCmsLogin = true, 
    // TCMSFieldNumber
/** @var int - Maximum displayed tasks */
private int $taskShowCount = 5, 
    // TCMSFieldBoolean
/** @var bool - Required by the system */
private bool $isSystem = false, 
    // TCMSFieldBoolean
/** @var bool - Can be used as a rights template */
private bool $showAsRightsTemplate = false, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMenuItem[] - Used menu entries */
private \Doctrine\Common\Collections\Collection $cmsMenuItemMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldTimestamp
/** @var \DateTime|null -  */
private \DateTime|null $dateModified = null, 
    // TCMSFieldVarchar
/** @var string - Google User ID */
private string $googleId = ''  ) {}

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


  
    // TCMSFieldPasswordEncrypted
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


  
    // TCMSFieldEmail
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
public function getImages(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->images;
}
public function setImages(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $images): self
{
    $this->images = $images;
    $this->imagesId = $images?->getId();

    return $this;
}
public function getImagesId(): ?string
{
    return $this->imagesId;
}
public function setImagesId(?string $imagesId): self
{
    $this->imagesId = $imagesId;
    // todo - load new id
    //$this->imagesId = $?->getId();

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


  
    // TCMSFieldLookupGroups
public function getCmsUsergroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsUsergroupMlt;
}
public function setCmsUsergroupMlt(\Doctrine\Common\Collections\Collection $cmsUsergroupMlt): self
{
    $this->cmsUsergroupMlt = $cmsUsergroupMlt;

    return $this;
}


  
    // TCMSFieldLookupRoles
public function getCmsRoleMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRoleMlt;
}
public function setCmsRoleMlt(\Doctrine\Common\Collections\Collection $cmsRoleMlt): self
{
    $this->cmsRoleMlt = $cmsRoleMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsPortalMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalMlt;
}
public function setCmsPortalMlt(\Doctrine\Common\Collections\Collection $cmsPortalMlt): self
{
    $this->cmsPortalMlt = $cmsPortalMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages
public function getCmsLanguageMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsLanguageMlt;
}
public function setCmsLanguageMlt(\Doctrine\Common\Collections\Collection $cmsLanguageMlt): self
{
    $this->cmsLanguageMlt = $cmsLanguageMlt;

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


  
    // TCMSFieldBoolean
public function isAllowCmsLogin(): bool
{
    return $this->allowCmsLogin;
}
public function setAllowCmsLogin(bool $allowCmsLogin): self
{
    $this->allowCmsLogin = $allowCmsLogin;

    return $this;
}


  
    // TCMSFieldNumber
public function getTaskShowCount(): int
{
    return $this->taskShowCount;
}
public function setTaskShowCount(int $taskShowCount): self
{
    $this->taskShowCount = $taskShowCount;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsSystem(): bool
{
    return $this->isSystem;
}
public function setIsSystem(bool $isSystem): self
{
    $this->isSystem = $isSystem;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowAsRightsTemplate(): bool
{
    return $this->showAsRightsTemplate;
}
public function setShowAsRightsTemplate(bool $showAsRightsTemplate): self
{
    $this->showAsRightsTemplate = $showAsRightsTemplate;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getCmsMenuItemMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMenuItemMlt;
}
public function setCmsMenuItemMlt(\Doctrine\Common\Collections\Collection $cmsMenuItemMlt): self
{
    $this->cmsMenuItemMlt = $cmsMenuItemMlt;

    return $this;
}


  
    // TCMSFieldTimestamp
public function getDateModified(): \DateTime|null
{
    return $this->dateModified;
}
public function setDateModified(\DateTime|null $dateModified): self
{
    $this->dateModified = $dateModified;

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
