<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopShippingType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Additional costs */
    public readonly float $value, 
    /** Addtional costs type */
    public readonly string $valueType, 
    /** Value relates to the whole basket */
    public readonly bool $valueBasedOnEntireBasket, 
    /** Additional charges */
    public readonly float $valueAdditional, 
    /** Maximum additional charges */
    public readonly float $valueMax, 
    /** Minimum additional charges */
    public readonly float $valueMin, 
    /** Calculate shipping costs for each item separately */
    public readonly bool $addValueForEachArticle, 
    /** Use for logged in users only */
    public readonly bool $restrictToSignedInUsers, 
    /** Apply to all products with at least one match */
    public readonly bool $applyToAllProducts, 
    /** When applied, ignore all other shipping costs types */
    public readonly bool $endShippingTypeChain, 
    /** Position */
    public readonly int $position, 
    /** Active */
    public readonly bool $active, 
    /** Active as of */
    public readonly \DateTime $activeFrom, 
    /** Active until */
    public readonly \DateTime $activeTo, 
    /** Minimum value of affected items (Euro) */
    public readonly float $restrictToValueFrom, 
    /** Maximum value of affected items (Euro) */
    public readonly float $restrictToValueTo, 
    /** Minimum amount of items affected */
    public readonly string $restrictToArticlesFrom, 
    /** Maximum amount of items affected */
    public readonly string $restrictToArticlesTo, 
    /** Minimum weight of affected items (grams) */
    public readonly string $restrictToWeightFrom, 
    /** Maximum weight of affected items (grams) */
    public readonly string $restrictToWeightTo, 
    /** Minimum volume of affected items (cubic meters) */
    public readonly float $restrictToVolumeFrom, 
    /** Maximum volume of affected items (cubic meters) */
    public readonly float $restrictToVolumeTo, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] Restrict to following product groups */
    public readonly array $shopArticleGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Restrict to following product categories */
    public readonly array $shopCategoryMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Restrict to following items */
    public readonly array $shopArticleMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] Restrict to following shipping countries */
    public readonly array $dataCountryMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] Restrict to following users */
    public readonly array $dataExtranetUserMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Restrict to following customer groups */
    public readonly array $dataExtranetGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Restrict to following portals */
    public readonly array $cmsPortalMlt  ) {}
}