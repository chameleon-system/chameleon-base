<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderShippingGroupParameter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Parameter name */
    public readonly string $name, 
    /** Value */
    public readonly string $value  ) {}
}