<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderDiscount {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Order ID */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Discount ID */
    public readonly string $shopDiscountId, 
    /** Name */
    public readonly string $name, 
    /** Value */
    public readonly string $value, 
    /** Value type */
    public readonly string $valuetype, 
    /** Gratis article (name) */
    public readonly string $freearticleName, 
    /** Gratis article (article number) */
    public readonly string $freearticleArticlenumber, 
    /** Gratis article (ID) */
    public readonly string $freearticleId, 
    /** Total */
    public readonly float $total  ) {}
}