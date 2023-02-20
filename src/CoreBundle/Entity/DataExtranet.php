<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranet {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Portal configuration */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Portal configuration */
private ?string $cmsPortalId = null
, 
    // TCMSFieldNumber
/** @var int - Session lifetime (in seconds) */
private int $sessionlife = 3600, 
    // TCMSFieldVarchar
/** @var string - Title */
private string $fpwdTitle = '', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $noaccessTitle = '', 
    // TCMSFieldBoolean
/** @var bool - Login must be an email address */
private bool $loginIsEmail = true, 
    // TCMSFieldVarchar
/** @var string - Name of the spot where an extranet module is available */
private string $extranetSpotName = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Automatically assign new customers to these groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Login */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeLoginId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Login successful */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $loginSuccessNodeId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - My account */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeMyAccountCmsTreeId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Registration */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeRegisterId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Confirm registration */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeConfirmRegistration = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Registration successful */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeRegistrationSuccessId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Forgot password */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $forgotPasswordTreenodeId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Access denied (not signed in) */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $accessRefusedNodeId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Access denied (group permissons) */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $groupRightDeniedNodeId = null, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Logout successful */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $logoutSuccessNodeId = null, 
    // TCMSFieldWYSIWYG
/** @var string - Registration successful */
private string $registrationSuccess = '', 
    // TCMSFieldWYSIWYG
/** @var string - Registration failed */
private string $registrationFailed = '', 
    // TCMSFieldBoolean
/** @var bool - Users must confirm their registration */
private bool $userMustConfirmRegistration = false, 
    // TCMSFieldWYSIWYG
/** @var string - Header */
private string $fpwdIntro = '', 
    // TCMSFieldWYSIWYG
/** @var string - Footer */
private string $fpwdEnd = '', 
    // TCMSFieldWYSIWYG
/** @var string - Text */
private string $noaccessText = '', 
    // TCMSFieldWYSIWYG
/** @var string - Text to be displayed after login to the community */
private string $communityPostRegistrationInfo = '', 
    // TCMSFieldBoolean
/** @var bool - Use forgot password, get new password method */
private bool $useSaveForgotPassword = true, 
    // TCMSFieldBoolean
/** @var bool - Enable login for non-confirmed users */
private bool $loginAllowedNotConfirmedUser = false, 
    // TCMSFieldNumber
/** @var int - Validity of the password change key (in hours) */
private int $passwordChangeKeyTimeValidity = 2  ) {}

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
    // TCMSFieldNumber
public function getSessionlife(): int
{
    return $this->sessionlife;
}
public function setSessionlife(int $sessionlife): self
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



  
    // TCMSFieldBoolean
public function isLoginIsEmail(): bool
{
    return $this->loginIsEmail;
}
public function setLoginIsEmail(bool $loginIsEmail): self
{
    $this->loginIsEmail = $loginIsEmail;

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


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getDataExtranetGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetGroupMlt;
}
public function setDataExtranetGroupMlt(\Doctrine\Common\Collections\Collection $dataExtranetGroupMlt): self
{
    $this->dataExtranetGroupMlt = $dataExtranetGroupMlt;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getNodeLoginId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->nodeLoginId;
}
public function setNodeLoginId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeLoginId): self
{
    $this->nodeLoginId = $nodeLoginId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getLoginSuccessNodeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->loginSuccessNodeId;
}
public function setLoginSuccessNodeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $loginSuccessNodeId): self
{
    $this->loginSuccessNodeId = $loginSuccessNodeId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getNodeMyAccountCmsTreeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->nodeMyAccountCmsTreeId;
}
public function setNodeMyAccountCmsTreeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeMyAccountCmsTreeId): self
{
    $this->nodeMyAccountCmsTreeId = $nodeMyAccountCmsTreeId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getNodeRegisterId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->nodeRegisterId;
}
public function setNodeRegisterId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeRegisterId): self
{
    $this->nodeRegisterId = $nodeRegisterId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getNodeConfirmRegistration(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->nodeConfirmRegistration;
}
public function setNodeConfirmRegistration(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeConfirmRegistration): self
{
    $this->nodeConfirmRegistration = $nodeConfirmRegistration;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getNodeRegistrationSuccessId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->nodeRegistrationSuccessId;
}
public function setNodeRegistrationSuccessId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $nodeRegistrationSuccessId): self
{
    $this->nodeRegistrationSuccessId = $nodeRegistrationSuccessId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getForgotPasswordTreenodeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->forgotPasswordTreenodeId;
}
public function setForgotPasswordTreenodeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $forgotPasswordTreenodeId): self
{
    $this->forgotPasswordTreenodeId = $forgotPasswordTreenodeId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getAccessRefusedNodeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->accessRefusedNodeId;
}
public function setAccessRefusedNodeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $accessRefusedNodeId): self
{
    $this->accessRefusedNodeId = $accessRefusedNodeId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getGroupRightDeniedNodeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->groupRightDeniedNodeId;
}
public function setGroupRightDeniedNodeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $groupRightDeniedNodeId): self
{
    $this->groupRightDeniedNodeId = $groupRightDeniedNodeId;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getLogoutSuccessNodeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->logoutSuccessNodeId;
}
public function setLogoutSuccessNodeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $logoutSuccessNodeId): self
{
    $this->logoutSuccessNodeId = $logoutSuccessNodeId;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getRegistrationSuccess(): string
{
    return $this->registrationSuccess;
}
public function setRegistrationSuccess(string $registrationSuccess): self
{
    $this->registrationSuccess = $registrationSuccess;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getRegistrationFailed(): string
{
    return $this->registrationFailed;
}
public function setRegistrationFailed(string $registrationFailed): self
{
    $this->registrationFailed = $registrationFailed;

    return $this;
}


  
    // TCMSFieldBoolean
public function isUserMustConfirmRegistration(): bool
{
    return $this->userMustConfirmRegistration;
}
public function setUserMustConfirmRegistration(bool $userMustConfirmRegistration): self
{
    $this->userMustConfirmRegistration = $userMustConfirmRegistration;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getFpwdIntro(): string
{
    return $this->fpwdIntro;
}
public function setFpwdIntro(string $fpwdIntro): self
{
    $this->fpwdIntro = $fpwdIntro;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getFpwdEnd(): string
{
    return $this->fpwdEnd;
}
public function setFpwdEnd(string $fpwdEnd): self
{
    $this->fpwdEnd = $fpwdEnd;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getNoaccessText(): string
{
    return $this->noaccessText;
}
public function setNoaccessText(string $noaccessText): self
{
    $this->noaccessText = $noaccessText;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getCommunityPostRegistrationInfo(): string
{
    return $this->communityPostRegistrationInfo;
}
public function setCommunityPostRegistrationInfo(string $communityPostRegistrationInfo): self
{
    $this->communityPostRegistrationInfo = $communityPostRegistrationInfo;

    return $this;
}


  
    // TCMSFieldBoolean
public function isUseSaveForgotPassword(): bool
{
    return $this->useSaveForgotPassword;
}
public function setUseSaveForgotPassword(bool $useSaveForgotPassword): self
{
    $this->useSaveForgotPassword = $useSaveForgotPassword;

    return $this;
}


  
    // TCMSFieldBoolean
public function isLoginAllowedNotConfirmedUser(): bool
{
    return $this->loginAllowedNotConfirmedUser;
}
public function setLoginAllowedNotConfirmedUser(bool $loginAllowedNotConfirmedUser): self
{
    $this->loginAllowedNotConfirmedUser = $loginAllowedNotConfirmedUser;

    return $this;
}


  
    // TCMSFieldNumber
public function getPasswordChangeKeyTimeValidity(): int
{
    return $this->passwordChangeKeyTimeValidity;
}
public function setPasswordChangeKeyTimeValidity(int $passwordChangeKeyTimeValidity): self
{
    $this->passwordChangeKeyTimeValidity = $passwordChangeKeyTimeValidity;

    return $this;
}


  
}
