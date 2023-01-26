<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStatusItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to status */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrderStatus $shopOrderStatusId, 
    /** Product */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrderItem $shopOrderItemId, 
    /** Amount */
    public readonly float $amount  ) {}
}