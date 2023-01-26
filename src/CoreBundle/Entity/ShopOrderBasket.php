<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderBasket {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Basket cart ID (will also be included in the order) */
    public readonly string $orderIdent, 
    /** Session ID */
    public readonly string $sessionId, 
    /** Created on */
    public readonly string $datecreated, 
    /** Last changed */
    public readonly string $lastmodified, 
    /** Basket */
    public readonly string $rawdataBasket, 
    /** User data */
    public readonly string $rawdataUser, 
    /** Session */
    public readonly string $rawdataSession, 
    /** Order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Last update in step */
    public readonly string $updateStepname, 
    /** Processed */
    public readonly bool $processed  ) {}
}