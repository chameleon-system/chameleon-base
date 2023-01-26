<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopAttributeValue {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to the attribute */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopAttribute $shopAttributeId, 
    /** Value */
    public readonly string $name, 
    /** Sorting */
    public readonly int $position  ) {}
}