<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticle {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Accessories  */
    public readonly array $shopArticle2Mlt, 
    /** SEO pattern */
    public readonly string $seoPattern, 
    /** Product number */
    public readonly string $articlenumber, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleImage[] Detailed product pictures */
    public readonly array $shopArticleImage, 
    /** Default preview image of the product */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaDefaultPreviewImageId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticlePreviewImage[] Product preview images */
    public readonly array $shopArticlePreviewImage, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleDocument[] Product documents */
    public readonly array $shopArticleDocument, 
    /** Quantifier / Product ranking */
    public readonly string $listRank, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** Active */
    public readonly bool $active, 
    /** Short description */
    public readonly string $descriptionShort, 
    /** Description */
    public readonly string $description, 
    /** Manufacturer / Brand */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopManufacturer $shopManufacturerId, 
    /** Price */
    public readonly float $price, 
    /** Reference price */
    public readonly float $priceReference, 
    /** VAT group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVat $shopVatId, 
    /** Weight (grams) */
    public readonly float $sizeWeight, 
    /** Width (meters) */
    public readonly float $sizeWidth, 
    /** Height (meters) */
    public readonly float $sizeHeight, 
    /** Length (meters) */
    public readonly float $sizeLength, 
    /** Content */
    public readonly float $quantityInUnits, 
    /** Measurement unit of content */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement $shopUnitOfMeasurementId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleStock[] Stock */
    public readonly array $shopArticleStock, 
    /** Offer preorder at 0 stock */
    public readonly bool $showPreorderOnZeroStock, 
    /** Delivery status */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopStockMessage $shopStockMessageId, 
    /** Virtual product */
    public readonly bool $virtualArticle, 
    /** Is searchable */
    public readonly bool $isSearchable, 
    /** Product is free of shipping costs */
    public readonly bool $excludeFromShippingCostCalculation, 
    /** Do not allow vouchers */
    public readonly bool $excludeFromVouchers, 
    /** Do not allow discounts */
    public readonly bool $excludeFromDiscounts, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] Product groups */
    public readonly array $shopArticleGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleType[] Product type */
    public readonly array $shopArticleTypeMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Product categories */
    public readonly array $shopCategoryMlt, 
    /** Main category of the product */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopCategory $shopCategoryId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleMarker[] Product characteristics */
    public readonly array $shopArticleMarkerMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopAttributeValue[] Product attributes */
    public readonly array $shopAttributeValueMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\Shop[] Restrict to the following shops */
    public readonly array $shopMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleContributor[] Contributing persons */
    public readonly array $shopArticleContributor, 
    /** Subtitle */
    public readonly string $subtitle, 
    /** Mark as new */
    public readonly bool $isNew, 
    /** USP */
    public readonly string $usp, 
    /** Number of stars */
    public readonly string $stars, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleReview[] Customer reviews */
    public readonly array $shopArticleReview, 
    /** Is a bundle */
    public readonly bool $isBundle, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopBundleArticle[] Items belonging to this bundle */
    public readonly array $shopBundleArticle, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] Download file */
    public readonly array $download, 
    /** Variant name */
    public readonly string $nameVariantInfo, 
    /** Variant set */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVariantSet $shopVariantSetId, 
    /** Is a variant of */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $variantParentId, 
    /** Is the parent of the variant active? */
    public readonly bool $variantParentIsActive, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Product variants */
    public readonly array $shopArticleVariants, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantTypeValue[] Variant values */
    public readonly array $shopVariantTypeValueMlt, 
    /** Meta keywords */
    public readonly string $metaKeywords, 
    /** Meta description */
    public readonly string $metaDescription, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] Similar products */
    public readonly array $shopArticleMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTags[] Tag / Catchword */
    public readonly array $cmsTagsMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleStats[] Statistics */
    public readonly array $shopArticleStats  ) {}
}