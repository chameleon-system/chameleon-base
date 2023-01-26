<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentIpnMessageTrigger {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Trigger */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger $pkgShopPaymentIpnTriggerId, 
    /** IPN Message */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage $pkgShopPaymentIpnMessageId, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** Processed */
    public readonly bool $done, 
    /** Processed on */
    public readonly \DateTime $doneDate, 
    /** Successful */
    public readonly bool $success, 
    /** Number of attempts */
    public readonly string $attemptCount, 
    /** Next attempt on */
    public readonly \DateTime $nextAttempt, 
    /** Log */
    public readonly string $log  ) {}
}