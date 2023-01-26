<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVoucherSeries {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Voucher sponsor */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeriesSponsor $shopVoucherSeriesSponsorId, 
    /** Value */
    public readonly float $value, 
    /** Value type */
    public readonly string $valueType, 
    /** VAT group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVat $shopVatId, 
    /** Free shipping */
    public readonly bool $freeShipping, 
    /** Active */
    public readonly bool $active, 
    /** Active from */
    public readonly \DateTime $activeFrom, 
    /** Active until */
    public readonly \DateTime $activeTo, 
    /** Minimum order value */
    public readonly float $restrictToValue, 
    /** Allow with other series only */
    public readonly bool $restrictToOtherSeries, 
    /** Do not allow in combination with other vouchers */
    public readonly bool $allowNoOtherVouchers, 
    /** Allow one voucher per customer only */
    public readonly bool $restrictToOnePerUser, 
    /** Only allow at first order of a customer */
    public readonly bool $restrictToFirstOrder, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] Restrict to following customers */
    public readonly array $dataExtranetUserMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Restrict to following customer groups */
    public readonly array $dataExtranetGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopManufacturer[] Restrict to products from this manufacturer */
    public readonly array $shopManufacturerMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] Restrict to products from these product groups */
    public readonly array $shopArticleGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Restrict to products from these product categories */
    public readonly array $shopCategoryMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Restrict to these products */
    public readonly array $shopArticleMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucher[] Vouchers belonging to the series */
    public readonly array $shopVoucher  ) {}
}