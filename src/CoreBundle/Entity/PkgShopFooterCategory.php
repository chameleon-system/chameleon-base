<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopFooterCategory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Main category / heading */
    public readonly string $name, 
    /** Product category */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopCategory $shopCategoryId, 
    /** Sorting */
    public readonly int $sortOrder, 
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId  ) {}
}