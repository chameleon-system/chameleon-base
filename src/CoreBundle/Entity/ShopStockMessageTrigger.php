<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopStockMessageTrigger {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Stock message */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopStockMessage $shopStockMessageId, 
    /** Amount */
    public readonly string $amount, 
    /** Message */
    public readonly string $message, 
    /** System name */
    public readonly string $systemName, 
    /** CSS class */
    public readonly string $cssClass  ) {}
}