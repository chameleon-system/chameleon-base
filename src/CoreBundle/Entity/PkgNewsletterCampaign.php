<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterCampaign {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Campaign source (utm_source) */
    public readonly string $utmSource, 
    /** Campaign medium (utm_medium) */
    public readonly string $utmMedium, 
    /** Campaign content (utm_content) */
    public readonly string $utmContent, 
    /** Campaign name (utm_campaign) */
    public readonly string $utmCampaign, 
    /** Newsletter title */
    public readonly string $name, 
    /** Newlsetter template page */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $cmsTreeNodeId, 
    /** Newsletter queue active */
    public readonly bool $active, 
    /** Subject */
    public readonly string $subject, 
    /** Portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterQueue[] Queue items */
    public readonly array $pkgNewsletterQueue, 
    /** Content text */
    public readonly string $contentPlain, 
    /** Desired shipping time */
    public readonly \DateTime $queueDate, 
    /** Send status */
    public readonly string $sendStatistics, 
    /** Start of shipping */
    public readonly \DateTime $sendStartDate, 
    /** End of shipping */
    public readonly \DateTime $sendEndDate, 
    /** Generate user-specific newsletters */
    public readonly bool $generateUserDependingNewsletter, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup[] Recipient list */
    public readonly array $pkgNewsletterGroupMlt, 
    /** Enable Google Analytics tagging */
    public readonly bool $googleAnalyticsActive  ) {}
}