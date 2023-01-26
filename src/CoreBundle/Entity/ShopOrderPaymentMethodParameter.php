<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderPaymentMethodParameter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Name */
    public readonly string $name, 
    /** Value */
    public readonly string $value  ) {}
}