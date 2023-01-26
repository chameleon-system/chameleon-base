<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentMethod {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to payment provider */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup $shopPaymentHandlerGroupId, 
    /** Name */
    public readonly string $name, 
    /** Internal system name */
    public readonly string $nameInternal, 
    /** Payment handler */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler $shopPaymentHandlerId, 
    /** Active */
    public readonly bool $active, 
    /** Allow for Packstation delivery addresses */
    public readonly bool $pkgDhlPackstationAllowForPackstation, 
    /** Sorting */
    public readonly int $position, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Restrict to the following portals */
    public readonly array $cmsPortalMlt, 
    /** Available from merchandise value */
    public readonly float $restrictToValueFrom, 
    /** Available until merchandise value */
    public readonly float $restrictToValueTo, 
    /** Available from basket value */
    public readonly float $restrictToBasketValueFrom, 
    /** Available to basket value */
    public readonly float $restrictToBasketValueTo, 
    /** Additional costs */
    public readonly float $value, 
    /** Additional costs type */
    public readonly string $valueType, 
    /** VAT group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVat $shopVatId, 
    /** Icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Description */
    public readonly string $description, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] Restrict to following customers */
    public readonly array $dataExtranetUserMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Restrict to following customer groups */
    public readonly array $dataExtranetGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] Restrict to following shipping countries */
    public readonly array $dataCountryMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] Restrict to following billing countries */
    public readonly array $dataCountryBilling, 
    /** Use not fixed positive list match */
    public readonly bool $positivListLooseMatch, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] Restrict to following product groups */
    public readonly array $shopArticleGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Restrict to following product categories */
    public readonly array $shopCategoryMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Restrict to following items */
    public readonly array $shopArticleMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] Do not allow for following product groups */
    public readonly array $shopArticleGroup1Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Do not allow for following product categories */
    public readonly array $shopCategory1Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Do not allow for following products */
    public readonly array $shopArticle1Mlt  ) {}
}