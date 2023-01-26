<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderVat {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Name */
    public readonly string $name, 
    /** Percent */
    public readonly float $vatPercent, 
    /** Value for order */
    public readonly float $value  ) {}
}