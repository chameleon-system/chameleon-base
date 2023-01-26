<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopModuleArticleListArticle {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to article list */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleList $shopModuleArticleListId, 
    /** Article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Position */
    public readonly int $position, 
    /** Alternative headline */
    public readonly string $name  ) {}
}