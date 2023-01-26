<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderExportLog {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** IP */
    public readonly string $ip, 
    /** Data */
    public readonly string $data, 
    /** Session ID */
    public readonly string $userSessionId  ) {}
}