<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterUser {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to customer */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup[] Subscriber of recipient lists */
    public readonly array $pkgNewsletterGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterConfirmation[] Confirmations */
    public readonly array $pkgNewsletterConfirmationMlt, 
    /** Email address */
    public readonly string $email, 
    /** Write delete log */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation $dataExtranetSalutationId, 
    /** First name */
    public readonly string $firstname, 
    /** Last name */
    public readonly string $lastname, 
    /** Company */
    public readonly string $company, 
    /** Portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Subscription date */
    public readonly \DateTime $signupDate, 
    /** Confirmation code */
    public readonly string $optincode, 
    /** Subscription confirmed */
    public readonly bool $optin, 
    /** Confirmed on */
    public readonly \DateTime $optinDate, 
    /** Unsubscription code */
    public readonly string $optoutcode  ) {}
}