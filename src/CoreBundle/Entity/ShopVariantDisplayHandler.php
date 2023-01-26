<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVariantDisplayHandler {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Class */
    public readonly string $class, 
    /** Class subtype */
    public readonly string $classSubtype, 
    /** Class type */
    public readonly string $classType  ) {}
}