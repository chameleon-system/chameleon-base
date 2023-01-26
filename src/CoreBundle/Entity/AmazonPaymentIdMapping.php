<?php
namespace ChameleonSystem\CoreBundle\Entity;

class AmazonPaymentIdMapping {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Amazon order reference ID */
    public readonly string $amazonOrderReferenceId, 
    /** Local reference ID */
    public readonly string $localId, 
    /** Amazon ID */
    public readonly string $amazonId, 
    /** Value */
    public readonly float $value, 
    /** Type */
    public readonly string $type, 
    /** Request mode */
    public readonly string $requestMode, 
    /** CaptureNow */
    public readonly bool $captureNow, 
    /** Belongs to transaction */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction $pkgShopPaymentTransactionId  ) {}
}