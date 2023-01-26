<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopCurrency {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Symbol */
    public readonly string $symbol, 
    /** Conversion factor */
    public readonly float $factor, 
    /** Is the base currency */
    public readonly bool $isBaseCurrency, 
    /** ISO-4217 Code */
    public readonly string $iso4217  ) {}
}