<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopDiscount {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Value */
    public readonly float $value, 
    /** Value type */
    public readonly string $valueType, 
    /** Show percentual discount on detailed product page */
    public readonly bool $showDiscountOnArticleDetailpage, 
    /** Active */
    public readonly bool $active, 
    /** Valid from */
    public readonly \DateTime $activeFrom, 
    /** Active until */
    public readonly \DateTime $activeTo, 
    /** Sorting */
    public readonly int $position, 
    /** Min. amount of products affected */
    public readonly string $restrictToArticlesFrom, 
    /** Max. amount of products affected */
    public readonly string $restrictToArticlesTo, 
    /** Minimum value of affected products (Euro) */
    public readonly float $restrictToValueFrom, 
    /** Maximum value of affected products (Euro) */
    public readonly float $restrictToValueTo, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Restrict to following product categories */
    public readonly array $shopCategoryMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Restrict to following products */
    public readonly array $shopArticleMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Restrict to following customer groups */
    public readonly array $dataExtranetGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] Restrict to following customers */
    public readonly array $dataExtranetUserMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] Restrict to following shipping countries */
    public readonly array $dataCountryMlt, 
    /** Description */
    public readonly string $description, 
    /** When has the cache of the affected products been cleared the last time? */
    public readonly \DateTime $cacheClearLastExecuted  ) {}
}