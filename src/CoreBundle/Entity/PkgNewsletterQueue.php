<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterQueue {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Newsletter subscriber */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgNewsletterUser $pkgNewsletterUser, 
    /** Shipped on */
    public readonly \DateTime $dateSent, 
    /** Newsletter */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgNewsletterCampaign $pkgNewsletterCampaignId  ) {}
}