<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranet {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Session lifetime (in seconds) */
    public readonly string $sessionlife, 
    /** Title */
    public readonly string $fpwdTitle, 
    /** Title */
    public readonly string $noaccessTitle, 
    /** Portal configuration */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Login must be an email address */
    public readonly bool $loginIsEmail, 
    /** Name of the spot where an extranet module is available */
    public readonly string $extranetSpotName, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Automatically assign new customers to these groups */
    public readonly array $dataExtranetGroupMlt, 
    /** Login */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $nodeLoginId, 
    /** Login successful */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $loginSuccessNodeId, 
    /** My account */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $nodeMyAccountCmsTreeId, 
    /** Registration */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $nodeRegisterId, 
    /** Confirm registration */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $nodeConfirmRegistration, 
    /** Registration successful */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $nodeRegistrationSuccessId, 
    /** Forgot password */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $forgotPasswordTreenodeId, 
    /** Access denied (not signed in) */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $accessRefusedNodeId, 
    /** Access denied (group permissons) */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $groupRightDeniedNodeId, 
    /** Logout successful */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $logoutSuccessNodeId, 
    /** Registration successful */
    public readonly string $registrationSuccess, 
    /** Registration failed */
    public readonly string $registrationFailed, 
    /** Users must confirm their registration */
    public readonly bool $userMustConfirmRegistration, 
    /** Header */
    public readonly string $fpwdIntro, 
    /** Footer */
    public readonly string $fpwdEnd, 
    /** Text */
    public readonly string $noaccessText, 
    /** Text to be displayed after login to the community */
    public readonly string $communityPostRegistrationInfo, 
    /** Use forgot password, get new password method */
    public readonly bool $useSaveForgotPassword, 
    /** Enable login for non-confirmed users */
    public readonly bool $loginAllowedNotConfirmedUser, 
    /** Validity of the password change key (in hours) */
    public readonly string $passwordChangeKeyTimeValidity  ) {}
}