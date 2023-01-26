<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSystemInfo {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** System name */
    public readonly string $nameInternal, 
    /** Name */
    public readonly string $name, 
    /** Title */
    public readonly string $titel, 
    /** Content */
    public readonly string $content  ) {}
}