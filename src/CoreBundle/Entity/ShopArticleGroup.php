<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleGroup {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** VAT group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVat $shopVatId  ) {}
}