<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandler {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to payment provider */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup $shopPaymentHandlerGroupId, 
    /** Internal name for payment handler */
    public readonly string $name, 
    /** Block user selection */
    public readonly bool $blockUserSelection, 
    /** Class name */
    public readonly string $class, 
    /** Class type */
    public readonly string $classType, 
    /** Class subtype */
    public readonly string $classSubtype, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerParameter[] Configuration settings */
    public readonly array $shopPaymentHandlerParameter  ) {}
}