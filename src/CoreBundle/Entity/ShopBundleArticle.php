<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopBundleArticle {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to bundle article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $bundleArticleId, 
    /** Units */
    public readonly string $amount, 
    /** Position */
    public readonly int $position  ) {}
}