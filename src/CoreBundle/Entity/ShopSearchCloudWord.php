<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchCloudWord {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Word */
    public readonly string $name, 
    /** Percentage weight relative to real search terms */
    public readonly float $weight  ) {}
}