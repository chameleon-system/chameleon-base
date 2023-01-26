<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopContributorType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** ID code */
    public readonly string $identifier  ) {}
}