<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopContributor {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name  ) {}
}