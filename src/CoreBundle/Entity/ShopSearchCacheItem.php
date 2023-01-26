<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchCacheItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to search cache */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopSearchCache $shopSearchCacheId, 
    /** Weight */
    public readonly float $weight, 
    /** Article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId  ) {}
}