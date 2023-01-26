<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleCatalogConf {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title / headline */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConfDefaultOrder[] Alternative default sorting */
    public readonly array $shopArticleCatalogConfDefaultOrder, 
    /** Offer Reserving at 0 stock */
    public readonly bool $showSubcategoryProducts, 
    /** Articles per page */
    public readonly string $pageSize, 
    /** Default sorting */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby $shopModuleArticlelistOrderbyId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby[] Available sortings */
    public readonly array $shopModuleArticlelistOrderbyMlt, 
    /** Introduction text */
    public readonly string $intro  ) {}
}