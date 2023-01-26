<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVat {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Percentage */
    public readonly float $vatPercent  ) {}
}