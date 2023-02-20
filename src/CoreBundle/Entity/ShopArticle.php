<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticle {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Default preview image of the product */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMediaDefaultPreviewImage = null,
/** @var null|string - Default preview image of the product */
private ?string $cmsMediaDefaultPreviewImageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopManufacturer|null - Manufacturer / Brand */
private \ChameleonSystem\CoreBundle\Entity\ShopManufacturer|null $shopManufacturer = null,
/** @var null|string - Manufacturer / Brand */
private ?string $shopManufacturerId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVat|null - VAT group */
private \ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat = null,
/** @var null|string - VAT group */
private ?string $shopVatId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null - Measurement unit of content */
private \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null $shopUnitOfMeasurement = null,
/** @var null|string - Measurement unit of content */
private ?string $shopUnitOfMeasurementId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopStockMessage|null - Delivery status */
private \ChameleonSystem\CoreBundle\Entity\ShopStockMessage|null $shopStockMessage = null,
/** @var null|string - Delivery status */
private ?string $shopStockMessageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory|null - Main category of the product */
private \ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory = null,
/** @var null|string - Main category of the product */
private ?string $shopCategoryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null - Variant set */
private \ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null $shopVariantSet = null,
/** @var null|string - Variant set */
private ?string $shopVariantSetId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Is a variant of */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $variantParent = null,
/** @var null|string - Is a variant of */
private ?string $variantParentId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] - Accessories  */
private \Doctrine\Common\Collections\Collection $shopArticle2Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - SEO pattern */
private string $seoPattern = '', 
    // TCMSFieldVarchar
/** @var string - Product number */
private string $articlenumber = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleImage[] - Detailed product pictures */
private \Doctrine\Common\Collections\Collection $shopArticleImageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticlePreviewImage[] - Product preview images */
private \Doctrine\Common\Collections\Collection $shopArticlePreviewImageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleDocument[] - Product documents */
private \Doctrine\Common\Collections\Collection $shopArticleDocumentCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Quantifier / Product ranking */
private int $listRank = 0, 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldWYSIWYG
/** @var string - Short description */
private string $descriptionShort = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldPrice
/** @var float - Price */
private float $price = 0, 
    // TCMSFieldPrice
/** @var float - Reference price */
private float $priceReference = 0, 
    // TCMSFieldDecimal
/** @var float - Weight (grams) */
private float $sizeWeight = 0, 
    // TCMSFieldDecimal
/** @var float - Width (meters) */
private float $sizeWidth = 0, 
    // TCMSFieldDecimal
/** @var float - Height (meters) */
private float $sizeHeight = 0, 
    // TCMSFieldDecimal
/** @var float - Length (meters) */
private float $sizeLength = 0, 
    // TCMSFieldDecimal
/** @var float - Content */
private float $quantityInUnits = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleStock[] - Stock */
private \Doctrine\Common\Collections\Collection $shopArticleStockCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Offer preorder at 0 stock */
private bool $showPreorderOnZeroStock = false, 
    // TCMSFieldBoolean
/** @var bool - Virtual product */
private bool $virtualArticle = false, 
    // TCMSFieldBoolean
/** @var bool - Is searchable */
private bool $isSearchable = true, 
    // TCMSFieldBoolean
/** @var bool - Product is free of shipping costs */
private bool $excludeFromShippingCostCalculation = false, 
    // TCMSFieldBoolean
/** @var bool - Do not allow vouchers */
private bool $excludeFromVouchers = false, 
    // TCMSFieldBoolean
/** @var bool - Do not allow discounts */
private bool $excludeFromDiscounts = false, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] - Product groups */
private \Doctrine\Common\Collections\Collection $shopArticleGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleType[] - Product type */
private \Doctrine\Common\Collections\Collection $shopArticleTypeMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Product categories */
private \Doctrine\Common\Collections\Collection $shopCategoryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleMarker[] - Product characteristics */
private \Doctrine\Common\Collections\Collection $shopArticleMarkerMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopAttributeValue[] - Product attributes */
private \Doctrine\Common\Collections\Collection $shopAttributeValueMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\Shop[] - Restrict to the following shops */
private \Doctrine\Common\Collections\Collection $shopMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleContributor[] - Contributing persons */
private \Doctrine\Common\Collections\Collection $shopArticleContributorCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Subtitle */
private string $subtitle = '', 
    // TCMSFieldBoolean
/** @var bool - Mark as new */
private bool $isNew = false, 
    // TCMSFieldVarchar
/** @var string - USP */
private string $usp = '', 
    // TCMSFieldVarchar
/** @var string - Number of stars */
private string $stars = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleReview[] - Customer reviews */
private \Doctrine\Common\Collections\Collection $shopArticleReviewCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Is a bundle */
private bool $isBundle = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopBundleArticle[] - Items belonging to this bundle */
private \Doctrine\Common\Collections\Collection $shopBundleArticleCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldDownloads
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] - Download file */
private \Doctrine\Common\Collections\Collection $download = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Variant name */
private string $nameVariantInfo = '', 
    // TCMSFieldBoolean
/** @var bool - Is the parent of the variant active? */
private bool $variantParentIsActive = true, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] - Product variants */
private \Doctrine\Common\Collections\Collection $shopArticleVariantsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldShopVariantDetails
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantTypeValue[] - Variant values */
private \Doctrine\Common\Collections\Collection $shopVariantTypeValueMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Meta keywords */
private string $metaKeywords = '', 
    // TCMSFieldVarchar
/** @var string - Meta description */
private string $metaDescription = '', 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] - Similar products */
private \Doctrine\Common\Collections\Collection $shopArticleMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectTags
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTags[] - Tag / Catchword */
private \Doctrine\Common\Collections\Collection $cmsTagsMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleStats[] - Statistics */
private \Doctrine\Common\Collections\Collection $shopArticleStatsCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

  public function getId(): ?string
  {
    return $this->id;
  }
  public function setId(string $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function getCmsident(): ?int
  {
    return $this->cmsident;
  }
  public function setCmsident(int $cmsident): self
  {
    $this->cmsident = $cmsident;
    return $this;
  }
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopArticle2Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticle2Mlt;
}
public function setShopArticle2Mlt(\Doctrine\Common\Collections\Collection $shopArticle2Mlt): self
{
    $this->shopArticle2Mlt = $shopArticle2Mlt;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSeoPattern(): string
{
    return $this->seoPattern;
}
public function setSeoPattern(string $seoPattern): self
{
    $this->seoPattern = $seoPattern;

    return $this;
}


  
    // TCMSFieldVarchar
public function getArticlenumber(): string
{
    return $this->articlenumber;
}
public function setArticlenumber(string $articlenumber): self
{
    $this->articlenumber = $articlenumber;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopArticleImageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleImageCollection;
}
public function setShopArticleImageCollection(\Doctrine\Common\Collections\Collection $shopArticleImageCollection): self
{
    $this->shopArticleImageCollection = $shopArticleImageCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMediaDefaultPreviewImage(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMediaDefaultPreviewImage;
}
public function setCmsMediaDefaultPreviewImage(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMediaDefaultPreviewImage): self
{
    $this->cmsMediaDefaultPreviewImage = $cmsMediaDefaultPreviewImage;
    $this->cmsMediaDefaultPreviewImageId = $cmsMediaDefaultPreviewImage?->getId();

    return $this;
}
public function getCmsMediaDefaultPreviewImageId(): ?string
{
    return $this->cmsMediaDefaultPreviewImageId;
}
public function setCmsMediaDefaultPreviewImageId(?string $cmsMediaDefaultPreviewImageId): self
{
    $this->cmsMediaDefaultPreviewImageId = $cmsMediaDefaultPreviewImageId;
    // todo - load new id
    //$this->cmsMediaDefaultPreviewImageId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getShopArticlePreviewImageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticlePreviewImageCollection;
}
public function setShopArticlePreviewImageCollection(\Doctrine\Common\Collections\Collection $shopArticlePreviewImageCollection): self
{
    $this->shopArticlePreviewImageCollection = $shopArticlePreviewImageCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopArticleDocumentCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleDocumentCollection;
}
public function setShopArticleDocumentCollection(\Doctrine\Common\Collections\Collection $shopArticleDocumentCollection): self
{
    $this->shopArticleDocumentCollection = $shopArticleDocumentCollection;

    return $this;
}


  
    // TCMSFieldNumber
public function getListRank(): int
{
    return $this->listRank;
}
public function setListRank(int $listRank): self
{
    $this->listRank = $listRank;

    return $this;
}


  
    // TCMSFieldDateTimeNow
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

    return $this;
}


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescriptionShort(): string
{
    return $this->descriptionShort;
}
public function setDescriptionShort(string $descriptionShort): self
{
    $this->descriptionShort = $descriptionShort;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopManufacturer(): \ChameleonSystem\CoreBundle\Entity\ShopManufacturer|null
{
    return $this->shopManufacturer;
}
public function setShopManufacturer(\ChameleonSystem\CoreBundle\Entity\ShopManufacturer|null $shopManufacturer): self
{
    $this->shopManufacturer = $shopManufacturer;
    $this->shopManufacturerId = $shopManufacturer?->getId();

    return $this;
}
public function getShopManufacturerId(): ?string
{
    return $this->shopManufacturerId;
}
public function setShopManufacturerId(?string $shopManufacturerId): self
{
    $this->shopManufacturerId = $shopManufacturerId;
    // todo - load new id
    //$this->shopManufacturerId = $?->getId();

    return $this;
}



  
    // TCMSFieldPrice
public function getPrice(): float
{
    return $this->price;
}
public function setPrice(float $price): self
{
    $this->price = $price;

    return $this;
}


  
    // TCMSFieldPrice
public function getPriceReference(): float
{
    return $this->priceReference;
}
public function setPriceReference(float $priceReference): self
{
    $this->priceReference = $priceReference;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVat(): \ChameleonSystem\CoreBundle\Entity\ShopVat|null
{
    return $this->shopVat;
}
public function setShopVat(\ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat): self
{
    $this->shopVat = $shopVat;
    $this->shopVatId = $shopVat?->getId();

    return $this;
}
public function getShopVatId(): ?string
{
    return $this->shopVatId;
}
public function setShopVatId(?string $shopVatId): self
{
    $this->shopVatId = $shopVatId;
    // todo - load new id
    //$this->shopVatId = $?->getId();

    return $this;
}



  
    // TCMSFieldDecimal
public function getSizeWeight(): float
{
    return $this->sizeWeight;
}
public function setSizeWeight(float $sizeWeight): self
{
    $this->sizeWeight = $sizeWeight;

    return $this;
}


  
    // TCMSFieldDecimal
public function getSizeWidth(): float
{
    return $this->sizeWidth;
}
public function setSizeWidth(float $sizeWidth): self
{
    $this->sizeWidth = $sizeWidth;

    return $this;
}


  
    // TCMSFieldDecimal
public function getSizeHeight(): float
{
    return $this->sizeHeight;
}
public function setSizeHeight(float $sizeHeight): self
{
    $this->sizeHeight = $sizeHeight;

    return $this;
}


  
    // TCMSFieldDecimal
public function getSizeLength(): float
{
    return $this->sizeLength;
}
public function setSizeLength(float $sizeLength): self
{
    $this->sizeLength = $sizeLength;

    return $this;
}


  
    // TCMSFieldDecimal
public function getQuantityInUnits(): float
{
    return $this->quantityInUnits;
}
public function setQuantityInUnits(float $quantityInUnits): self
{
    $this->quantityInUnits = $quantityInUnits;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopUnitOfMeasurement(): \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null
{
    return $this->shopUnitOfMeasurement;
}
public function setShopUnitOfMeasurement(\ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null $shopUnitOfMeasurement): self
{
    $this->shopUnitOfMeasurement = $shopUnitOfMeasurement;
    $this->shopUnitOfMeasurementId = $shopUnitOfMeasurement?->getId();

    return $this;
}
public function getShopUnitOfMeasurementId(): ?string
{
    return $this->shopUnitOfMeasurementId;
}
public function setShopUnitOfMeasurementId(?string $shopUnitOfMeasurementId): self
{
    $this->shopUnitOfMeasurementId = $shopUnitOfMeasurementId;
    // todo - load new id
    //$this->shopUnitOfMeasurementId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getShopArticleStockCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleStockCollection;
}
public function setShopArticleStockCollection(\Doctrine\Common\Collections\Collection $shopArticleStockCollection): self
{
    $this->shopArticleStockCollection = $shopArticleStockCollection;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowPreorderOnZeroStock(): bool
{
    return $this->showPreorderOnZeroStock;
}
public function setShowPreorderOnZeroStock(bool $showPreorderOnZeroStock): self
{
    $this->showPreorderOnZeroStock = $showPreorderOnZeroStock;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopStockMessage(): \ChameleonSystem\CoreBundle\Entity\ShopStockMessage|null
{
    return $this->shopStockMessage;
}
public function setShopStockMessage(\ChameleonSystem\CoreBundle\Entity\ShopStockMessage|null $shopStockMessage): self
{
    $this->shopStockMessage = $shopStockMessage;
    $this->shopStockMessageId = $shopStockMessage?->getId();

    return $this;
}
public function getShopStockMessageId(): ?string
{
    return $this->shopStockMessageId;
}
public function setShopStockMessageId(?string $shopStockMessageId): self
{
    $this->shopStockMessageId = $shopStockMessageId;
    // todo - load new id
    //$this->shopStockMessageId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isVirtualArticle(): bool
{
    return $this->virtualArticle;
}
public function setVirtualArticle(bool $virtualArticle): self
{
    $this->virtualArticle = $virtualArticle;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsSearchable(): bool
{
    return $this->isSearchable;
}
public function setIsSearchable(bool $isSearchable): self
{
    $this->isSearchable = $isSearchable;

    return $this;
}


  
    // TCMSFieldBoolean
public function isExcludeFromShippingCostCalculation(): bool
{
    return $this->excludeFromShippingCostCalculation;
}
public function setExcludeFromShippingCostCalculation(bool $excludeFromShippingCostCalculation): self
{
    $this->excludeFromShippingCostCalculation = $excludeFromShippingCostCalculation;

    return $this;
}


  
    // TCMSFieldBoolean
public function isExcludeFromVouchers(): bool
{
    return $this->excludeFromVouchers;
}
public function setExcludeFromVouchers(bool $excludeFromVouchers): self
{
    $this->excludeFromVouchers = $excludeFromVouchers;

    return $this;
}


  
    // TCMSFieldBoolean
public function isExcludeFromDiscounts(): bool
{
    return $this->excludeFromDiscounts;
}
public function setExcludeFromDiscounts(bool $excludeFromDiscounts): self
{
    $this->excludeFromDiscounts = $excludeFromDiscounts;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopArticleGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleGroupMlt;
}
public function setShopArticleGroupMlt(\Doctrine\Common\Collections\Collection $shopArticleGroupMlt): self
{
    $this->shopArticleGroupMlt = $shopArticleGroupMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getShopArticleTypeMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleTypeMlt;
}
public function setShopArticleTypeMlt(\Doctrine\Common\Collections\Collection $shopArticleTypeMlt): self
{
    $this->shopArticleTypeMlt = $shopArticleTypeMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopCategoryMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopCategoryMlt;
}
public function setShopCategoryMlt(\Doctrine\Common\Collections\Collection $shopCategoryMlt): self
{
    $this->shopCategoryMlt = $shopCategoryMlt;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopCategory(): \ChameleonSystem\CoreBundle\Entity\ShopCategory|null
{
    return $this->shopCategory;
}
public function setShopCategory(\ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory): self
{
    $this->shopCategory = $shopCategory;
    $this->shopCategoryId = $shopCategory?->getId();

    return $this;
}
public function getShopCategoryId(): ?string
{
    return $this->shopCategoryId;
}
public function setShopCategoryId(?string $shopCategoryId): self
{
    $this->shopCategoryId = $shopCategoryId;
    // todo - load new id
    //$this->shopCategoryId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookupMultiselectCheckboxes
public function getShopArticleMarkerMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleMarkerMlt;
}
public function setShopArticleMarkerMlt(\Doctrine\Common\Collections\Collection $shopArticleMarkerMlt): self
{
    $this->shopArticleMarkerMlt = $shopArticleMarkerMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopAttributeValueMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopAttributeValueMlt;
}
public function setShopAttributeValueMlt(\Doctrine\Common\Collections\Collection $shopAttributeValueMlt): self
{
    $this->shopAttributeValueMlt = $shopAttributeValueMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getShopMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopMlt;
}
public function setShopMlt(\Doctrine\Common\Collections\Collection $shopMlt): self
{
    $this->shopMlt = $shopMlt;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopArticleContributorCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleContributorCollection;
}
public function setShopArticleContributorCollection(\Doctrine\Common\Collections\Collection $shopArticleContributorCollection): self
{
    $this->shopArticleContributorCollection = $shopArticleContributorCollection;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSubtitle(): string
{
    return $this->subtitle;
}
public function setSubtitle(string $subtitle): self
{
    $this->subtitle = $subtitle;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsNew(): bool
{
    return $this->isNew;
}
public function setIsNew(bool $isNew): self
{
    $this->isNew = $isNew;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUsp(): string
{
    return $this->usp;
}
public function setUsp(string $usp): self
{
    $this->usp = $usp;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStars(): string
{
    return $this->stars;
}
public function setStars(string $stars): self
{
    $this->stars = $stars;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopArticleReviewCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleReviewCollection;
}
public function setShopArticleReviewCollection(\Doctrine\Common\Collections\Collection $shopArticleReviewCollection): self
{
    $this->shopArticleReviewCollection = $shopArticleReviewCollection;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsBundle(): bool
{
    return $this->isBundle;
}
public function setIsBundle(bool $isBundle): self
{
    $this->isBundle = $isBundle;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopBundleArticleCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopBundleArticleCollection;
}
public function setShopBundleArticleCollection(\Doctrine\Common\Collections\Collection $shopBundleArticleCollection): self
{
    $this->shopBundleArticleCollection = $shopBundleArticleCollection;

    return $this;
}


  
    // TCMSFieldDownloads
public function getDownload(): \Doctrine\Common\Collections\Collection
{
    return $this->download;
}
public function setDownload(\Doctrine\Common\Collections\Collection $download): self
{
    $this->download = $download;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNameVariantInfo(): string
{
    return $this->nameVariantInfo;
}
public function setNameVariantInfo(string $nameVariantInfo): self
{
    $this->nameVariantInfo = $nameVariantInfo;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVariantSet(): \ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null
{
    return $this->shopVariantSet;
}
public function setShopVariantSet(\ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null $shopVariantSet): self
{
    $this->shopVariantSet = $shopVariantSet;
    $this->shopVariantSetId = $shopVariantSet?->getId();

    return $this;
}
public function getShopVariantSetId(): ?string
{
    return $this->shopVariantSetId;
}
public function setShopVariantSetId(?string $shopVariantSetId): self
{
    $this->shopVariantSetId = $shopVariantSetId;
    // todo - load new id
    //$this->shopVariantSetId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getVariantParent(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->variantParent;
}
public function setVariantParent(\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $variantParent): self
{
    $this->variantParent = $variantParent;
    $this->variantParentId = $variantParent?->getId();

    return $this;
}
public function getVariantParentId(): ?string
{
    return $this->variantParentId;
}
public function setVariantParentId(?string $variantParentId): self
{
    $this->variantParentId = $variantParentId;
    // todo - load new id
    //$this->variantParentId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isVariantParentIsActive(): bool
{
    return $this->variantParentIsActive;
}
public function setVariantParentIsActive(bool $variantParentIsActive): self
{
    $this->variantParentIsActive = $variantParentIsActive;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopArticleVariantsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleVariantsCollection;
}
public function setShopArticleVariantsCollection(\Doctrine\Common\Collections\Collection $shopArticleVariantsCollection): self
{
    $this->shopArticleVariantsCollection = $shopArticleVariantsCollection;

    return $this;
}


  
    // TCMSFieldShopVariantDetails
public function getShopVariantTypeValueMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopVariantTypeValueMlt;
}
public function setShopVariantTypeValueMlt(\Doctrine\Common\Collections\Collection $shopVariantTypeValueMlt): self
{
    $this->shopVariantTypeValueMlt = $shopVariantTypeValueMlt;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaKeywords(): string
{
    return $this->metaKeywords;
}
public function setMetaKeywords(string $metaKeywords): self
{
    $this->metaKeywords = $metaKeywords;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaDescription(): string
{
    return $this->metaDescription;
}
public function setMetaDescription(string $metaDescription): self
{
    $this->metaDescription = $metaDescription;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopArticleMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleMlt;
}
public function setShopArticleMlt(\Doctrine\Common\Collections\Collection $shopArticleMlt): self
{
    $this->shopArticleMlt = $shopArticleMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectTags
public function getCmsTagsMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTagsMlt;
}
public function setCmsTagsMlt(\Doctrine\Common\Collections\Collection $cmsTagsMlt): self
{
    $this->cmsTagsMlt = $cmsTagsMlt;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopArticleStatsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleStatsCollection;
}
public function setShopArticleStatsCollection(\Doctrine\Common\Collections\Collection $shopArticleStatsCollection): self
{
    $this->shopArticleStatsCollection = $shopArticleStatsCollection;

    return $this;
}


  
}
