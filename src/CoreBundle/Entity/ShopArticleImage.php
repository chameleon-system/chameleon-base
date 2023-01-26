<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleImage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Position */
    public readonly int $position  ) {}
}