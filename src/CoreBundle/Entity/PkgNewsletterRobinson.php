<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterRobinson {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Email address */
    public readonly string $email, 
    /** Reason for blacklisting */
    public readonly string $reason  ) {}
}