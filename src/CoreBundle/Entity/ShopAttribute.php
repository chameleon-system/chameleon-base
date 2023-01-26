<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopAttribute {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** System attributes */
    public readonly bool $isSystemAttribute, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopAttributeValue[] Attribute values */
    public readonly array $shopAttributeValue, 
    /** Internal name */
    public readonly string $systemName, 
    /** Description */
    public readonly string $description  ) {}
}