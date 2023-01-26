<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticlePreviewImage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Preview image size / type */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize $shopArticleImageSizeId, 
    /** Preview image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId  ) {}
}