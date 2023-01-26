<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopShippingGroupHandler {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Class name */
    public readonly string $class, 
    /** Class type */
    public readonly string $classType, 
    /** Class subtype */
    public readonly string $classSubtype  ) {}
}