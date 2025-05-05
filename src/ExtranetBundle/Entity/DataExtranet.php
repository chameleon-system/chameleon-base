<?php

namespace ChameleonSystem\ExtranetBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class DataExtranet
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldNumber
        /** @var int - Session lifetime (in seconds) */
        private int $sessionlife = 3600,
        // TCMSFieldVarchar
        /** @var string - Title */
        private string $fpwdTitle = '',
        // TCMSFieldVarchar
        /** @var string - Title */
        private string $noaccessTitle = '',
        // TCMSFieldLookup
        /** @var CmsPortal|null - Portal configuration */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldBoolean
        /** @var bool - Login must be an email address */
        private bool $loginIsEmail = true,
        // TCMSFieldVarchar
        /** @var string - Name of the spot where an extranet module is available */
        private string $extranetSpotName = '',
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, DataExtranetGroup> - Automatically assign new customers to these groups */
        private Collection $dataExtranetGroupCollection = new ArrayCollection(),
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Login */
        private ?CmsTree $nodeLogin = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Login successful */
        private ?CmsTree $loginSuccessNode = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - My account */
        private ?CmsTree $nodeMyAccountCmsTree = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Registration */
        private ?CmsTree $nodeRegister = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Confirm registration */
        private ?CmsTree $nodeConfirmRegistration = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Registration successful */
        private ?CmsTree $nodeRegistrationSuccess = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Forgot password */
        private ?CmsTree $forgotPasswordTreenode = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Access denied (not signed in) */
        private ?CmsTree $accessRefusedNode = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Access denied (group permissons) */
        private ?CmsTree $groupRightDeniedNode = null,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Logout successful */
        private ?CmsTree $logoutSuccessNode = null,
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
        private int $passwordChangeKeyTimeValidity = 2
    ) {
    }

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
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

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

    /**
     * @return Collection<int, DataExtranetGroup>
     */
    public function getDataExtranetGroupCollection(): Collection
    {
        return $this->dataExtranetGroupCollection;
    }

    public function addDataExtranetGroupCollection(DataExtranetGroup $dataExtranetGroupMlt): self
    {
        if (!$this->dataExtranetGroupCollection->contains($dataExtranetGroupMlt)) {
            $this->dataExtranetGroupCollection->add($dataExtranetGroupMlt);
            $dataExtranetGroupMlt->set($this);
        }

        return $this;
    }

    public function removeDataExtranetGroupCollection(DataExtranetGroup $dataExtranetGroupMlt): self
    {
        if ($this->dataExtranetGroupCollection->removeElement($dataExtranetGroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($dataExtranetGroupMlt->get() === $this) {
                $dataExtranetGroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldTreeNode
    public function getNodeLogin(): ?CmsTree
    {
        return $this->nodeLogin;
    }

    public function setNodeLogin(?CmsTree $nodeLogin): self
    {
        $this->nodeLogin = $nodeLogin;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getLoginSuccessNode(): ?CmsTree
    {
        return $this->loginSuccessNode;
    }

    public function setLoginSuccessNode(?CmsTree $loginSuccessNode): self
    {
        $this->loginSuccessNode = $loginSuccessNode;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getNodeMyAccountCmsTree(): ?CmsTree
    {
        return $this->nodeMyAccountCmsTree;
    }

    public function setNodeMyAccountCmsTree(?CmsTree $nodeMyAccountCmsTree): self
    {
        $this->nodeMyAccountCmsTree = $nodeMyAccountCmsTree;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getNodeRegister(): ?CmsTree
    {
        return $this->nodeRegister;
    }

    public function setNodeRegister(?CmsTree $nodeRegister): self
    {
        $this->nodeRegister = $nodeRegister;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getNodeConfirmRegistration(): ?CmsTree
    {
        return $this->nodeConfirmRegistration;
    }

    public function setNodeConfirmRegistration(?CmsTree $nodeConfirmRegistration): self
    {
        $this->nodeConfirmRegistration = $nodeConfirmRegistration;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getNodeRegistrationSuccess(): ?CmsTree
    {
        return $this->nodeRegistrationSuccess;
    }

    public function setNodeRegistrationSuccess(?CmsTree $nodeRegistrationSuccess): self
    {
        $this->nodeRegistrationSuccess = $nodeRegistrationSuccess;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getForgotPasswordTreenode(): ?CmsTree
    {
        return $this->forgotPasswordTreenode;
    }

    public function setForgotPasswordTreenode(?CmsTree $forgotPasswordTreenode): self
    {
        $this->forgotPasswordTreenode = $forgotPasswordTreenode;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getAccessRefusedNode(): ?CmsTree
    {
        return $this->accessRefusedNode;
    }

    public function setAccessRefusedNode(?CmsTree $accessRefusedNode): self
    {
        $this->accessRefusedNode = $accessRefusedNode;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getGroupRightDeniedNode(): ?CmsTree
    {
        return $this->groupRightDeniedNode;
    }

    public function setGroupRightDeniedNode(?CmsTree $groupRightDeniedNode): self
    {
        $this->groupRightDeniedNode = $groupRightDeniedNode;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getLogoutSuccessNode(): ?CmsTree
    {
        return $this->logoutSuccessNode;
    }

    public function setLogoutSuccessNode(?CmsTree $logoutSuccessNode): self
    {
        $this->logoutSuccessNode = $logoutSuccessNode;

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
