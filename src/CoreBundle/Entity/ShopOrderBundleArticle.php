<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderBundleArticle {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Bundle articles of the order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrderItem $shopOrderItemId, 
    /** Article belonging to bundle */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrderItem $bundleArticleId, 
    /** Units */
    public readonly string $amount, 
    /** Position */
    public readonly int $position  ) {}
}