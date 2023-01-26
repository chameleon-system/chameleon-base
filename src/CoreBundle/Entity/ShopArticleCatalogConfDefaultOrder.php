<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleCatalogConfDefaultOrder {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to configuration */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConf $shopArticleCatalogConfId, 
    /** Name (description) */
    public readonly string $name, 
    /** Sorting */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby $shopModuleArticlelistOrderbyId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Category */
    public readonly array $shopCategoryMlt  ) {}
}