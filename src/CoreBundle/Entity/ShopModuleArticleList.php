<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopModuleArticleList {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Release for the Post-Search-Filter */
    public readonly bool $canBeFiltered, 
    /** Headline */
    public readonly string $name, 
    /** Icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $icon, 
    /** Filter / content */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListFilter $shopModuleArticleListFilterId, 
    /** Sorting */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby $shopModuleArticlelistOrderbyId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby[] Available sortings */
    public readonly array $shopModuleArticlelistOrderbyMlt, 
    /** Number of articles shown */
    public readonly string $numberOfArticles, 
    /** Number of articles per page */
    public readonly string $numberOfArticlesPerPage, 
    /** Introduction text */
    public readonly string $descriptionStart, 
    /** Closing text */
    public readonly string $descriptionEnd, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] Show articles from these article groups */
    public readonly array $shopArticleGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Show articles from these product categories */
    public readonly array $shopCategoryMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListArticle[] Show these articles */
    public readonly array $shopModuleArticleListArticle  ) {}
}