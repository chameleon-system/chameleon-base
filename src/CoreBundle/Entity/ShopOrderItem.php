<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Variant */
    public readonly string $nameVariantInfo, 
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** sBasketItemKey is the key for the position in the consumer basket */
    public readonly string $basketItemKey, 
    /** Original article from shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Name */
    public readonly string $name, 
    /** Article number */
    public readonly string $articlenumber, 
    /** Short description */
    public readonly string $descriptionShort, 
    /** Description */
    public readonly string $description, 
    /** Manufacturer/ brand */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopManufacturer $shopManufacturerId, 
    /** Manufacturer / brand name */
    public readonly string $shopManufacturerName, 
    /** Price */
    public readonly float $price, 
    /** Reference price */
    public readonly float $priceReference, 
    /** Discounted price */
    public readonly float $priceDiscounted, 
    /** VAT percentage */
    public readonly float $vatPercent, 
    /** Weight (grams) */
    public readonly float $sizeWeight, 
    /** Width (meters) */
    public readonly float $sizeWidth, 
    /** Height (meters) */
    public readonly float $sizeHeight, 
    /** Length (meters) */
    public readonly float $sizeLength, 
    /** Stock at time of order */
    public readonly string $stock, 
    /** Units per packing */
    public readonly float $quantityInUnits, 
    /** Unit of measurement of content */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement $shopUnitOfMeasurementId, 
    /** Virtual article */
    public readonly bool $virtualArticle, 
    /** Do not allow vouchers */
    public readonly bool $excludeFromVouchers, 
    /** Do not allow discounts for this article */
    public readonly bool $excludeFromDiscounts, 
    /** Subtitle */
    public readonly string $subtitle, 
    /** Mark as new */
    public readonly bool $isNew, 
    /** Amount of pages */
    public readonly string $pages, 
    /** USP */
    public readonly string $usp, 
    /** Custom data */
    public readonly string $customData, 
    /** Amount */
    public readonly float $orderAmount, 
    /** Total price */
    public readonly float $orderPriceTotal, 
    /** Order price after calculation of discounts */
    public readonly float $orderPriceAfterDiscounts, 
    /** Total weight (grams) */
    public readonly float $orderTotalWeight, 
    /** Total volume (cubic meters) */
    public readonly float $orderTotalVolume, 
    /** Unit price at time of order */
    public readonly float $orderPrice, 
    /** Is a bundle */
    public readonly bool $isBundle, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderBundleArticle[] Articles in order that belong to this bundle */
    public readonly array $shopOrderBundleArticle, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] Download file */
    public readonly array $download  ) {}
}