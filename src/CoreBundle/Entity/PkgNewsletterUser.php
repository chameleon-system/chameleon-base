<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterUser {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Belongs to customer */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Belongs to customer */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null - Write delete log */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $dataExtranetSalutation = null,
/** @var null|string - Write delete log */
private ?string $dataExtranetSalutationId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup[] - Subscriber of recipient lists */
private \Doctrine\Common\Collections\Collection $pkgNewsletterGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterConfirmation[] - Confirmations */
private \Doctrine\Common\Collections\Collection $pkgNewsletterConfirmationMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldEmail
/** @var string - Email address */
private string $email = '', 
    // TCMSFieldVarchar
/** @var string - First name */
private string $firstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $lastname = '', 
    // TCMSFieldVarchar
/** @var string - Company */
private string $company = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Subscription date */
private \DateTime|null $signupDate = null, 
    // TCMSFieldVarchar
/** @var string - Confirmation code */
private string $optincode = '', 
    // TCMSFieldBoolean
/** @var bool - Subscription confirmed */
private bool $optin = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Confirmed on */
private \DateTime|null $optinDate = null, 
    // TCMSFieldVarchar
/** @var string - Unsubscription code */
private string $optoutcode = ''  ) {}

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



  
    // TCMSFieldLookupMultiselect
public function getPkgNewsletterGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgNewsletterGroupMlt;
}
public function setPkgNewsletterGroupMlt(\Doctrine\Common\Collections\Collection $pkgNewsletterGroupMlt): self
{
    $this->pkgNewsletterGroupMlt = $pkgNewsletterGroupMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getPkgNewsletterConfirmationMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgNewsletterConfirmationMlt;
}
public function setPkgNewsletterConfirmationMlt(\Doctrine\Common\Collections\Collection $pkgNewsletterConfirmationMlt): self
{
    $this->pkgNewsletterConfirmationMlt = $pkgNewsletterConfirmationMlt;

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
public function getDataExtranetSalutation(): \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null
{
    return $this->dataExtranetSalutation;
}
public function setDataExtranetSalutation(\ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $dataExtranetSalutation): self
{
    $this->dataExtranetSalutation = $dataExtranetSalutation;
    $this->dataExtranetSalutationId = $dataExtranetSalutation?->getId();

    return $this;
}
public function getDataExtranetSalutationId(): ?string
{
    return $this->dataExtranetSalutationId;
}
public function setDataExtranetSalutationId(?string $dataExtranetSalutationId): self
{
    $this->dataExtranetSalutationId = $dataExtranetSalutationId;
    // todo - load new id
    //$this->dataExtranetSalutationId = $?->getId();

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



  
    // TCMSFieldDateTime
public function getSignupDate(): \DateTime|null
{
    return $this->signupDate;
}
public function setSignupDate(\DateTime|null $signupDate): self
{
    $this->signupDate = $signupDate;

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


  
    // TCMSFieldBoolean
public function isOptin(): bool
{
    return $this->optin;
}
public function setOptin(bool $optin): self
{
    $this->optin = $optin;

    return $this;
}


  
    // TCMSFieldDateTime
public function getOptinDate(): \DateTime|null
{
    return $this->optinDate;
}
public function setOptinDate(\DateTime|null $optinDate): self
{
    $this->optinDate = $optinDate;

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
