<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopCategoryTab {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to category */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopCategory $shopCategoryId, 
    /** Name */
    public readonly string $name, 
    /** Description */
    public readonly string $description  ) {}
}