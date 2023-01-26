<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStatus {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Date */
    public readonly \DateTime $statusDate, 
    /** Status code */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode $shopOrderStatusCodeId, 
    /** Data */
    public readonly string $data, 
    /** Additional info */
    public readonly string $info, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusItem[] Order status items */
    public readonly array $shopOrderStatusItem  ) {}
}