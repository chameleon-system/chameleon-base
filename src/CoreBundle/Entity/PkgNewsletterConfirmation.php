<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterConfirmation {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Registration date */
    public readonly \DateTime $registrationDate, 
    /** Registration confirmed */
    public readonly bool $confirmation, 
    /** Registration confirmed on */
    public readonly \DateTime $confirmationDate, 
    /** Subscription to newsletter group */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup $pkgNewsletterGroupId, 
    /** Double opt-out key */
    public readonly string $optoutKey  ) {}
}