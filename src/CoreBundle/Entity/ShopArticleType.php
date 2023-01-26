<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** System identifier */
    public readonly string $identifier  ) {}
}