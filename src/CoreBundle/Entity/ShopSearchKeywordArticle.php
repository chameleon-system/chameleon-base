<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchKeywordArticle {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Keyword */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Articles */
    public readonly array $shopArticleMlt  ) {}
}