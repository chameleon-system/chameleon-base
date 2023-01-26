<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopStockMessage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Class */
    public readonly string $className, 
    /** Class subtype (path) */
    public readonly string $classSubtype, 
    /** Class type */
    public readonly string $classType, 
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Interface identifier */
    public readonly string $identifier, 
    /** CSS class */
    public readonly string $class, 
    /** Message */
    public readonly string $name, 
    /** System name */
    public readonly string $internalName, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopStockMessageTrigger[] Stock messages */
    public readonly array $shopStockMessageTrigger, 
    /** Automatically deactivate when stock = 0 */
    public readonly bool $autoDeactivateOnZeroStock, 
    /** Automatically deactivate when stock &gt; 0 */
    public readonly bool $autoActivateOnStock, 
    /** Google availability */
    public readonly string $googleAvailability  ) {}
}