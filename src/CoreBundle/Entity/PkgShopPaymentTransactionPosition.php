<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentTransactionPosition {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to transaction */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction $pkgShopPaymentTransactionId, 
    /** Amount */
    public readonly string $amount, 
    /** Value */
    public readonly float $value, 
    /** Type */
    public readonly string $type, 
    /** Order item */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrderItem $shopOrderItemId  ) {}
}