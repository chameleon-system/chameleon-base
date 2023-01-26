<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopUnitOfMeasurement {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Symbol / abbreviation */
    public readonly string $symbol, 
    /** Factor */
    public readonly float $factor, 
    /** Base unit */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement $shopUnitOfMeasurementId  ) {}
}