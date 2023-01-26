<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleStats {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Sales */
    public readonly string $statsSales, 
    /** Details on views */
    public readonly string $statsDetailViews, 
    /** Average rating */
    public readonly float $statsReviewAverage, 
    /** Number of ratings */
    public readonly string $statsReviewCount  ) {}
}