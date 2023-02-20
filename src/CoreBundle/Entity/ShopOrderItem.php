<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Belongs to order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Belongs to order */
private ?string $shopOrderId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Original article from shop */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Original article from shop */
private ?string $shopArticleId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopManufacturer|null - Manufacturer/ brand */
private \ChameleonSystem\CoreBundle\Entity\ShopManufacturer|null $shopManufacturer = null,
/** @var null|string - Manufacturer/ brand */
private ?string $shopManufacturerId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null - Unit of measurement of content */
private \ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement|null $shopUnitOfMeasurement = null,
/** @var null|string - Unit of measurement of content */
private ?string $shopUnitOfMeasurementId = null
, 
    // TCMSFieldVarchar
/** @var string - Variant */
private string $nameVariantInfo = '', 
    // TCMSFieldVarchar
/** @var string - sBasketItemKey is the key for the position in the consumer basket */
private string $basketItemKey = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Article number */
private string $articlenumber = '', 
    // TCMSFieldWYSIWYG
/** @var string - Short description */
private string $descriptionShort = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldVarchar
/** @var string - Manufacturer / brand name */
private string $shopManufacturerName = '', 
    // TCMSFieldDecimal
/** @var float - Price */
private float $price = 0, 
    // TCMSFieldDecimal
/** @var float - Reference price */
private float $priceReference = 0, 
    // TCMSFieldDecimal
/** @var float - Discounted price */
private float $priceDiscounted = 0, 
    // TCMSFieldDecimal
/** @var float - VAT percentage */
private float $vatPercent = 0, 
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
    // TCMSFieldNumber
/** @var int - Stock at time of order */
private int $stock = 0, 
    // TCMSFieldDecimal
/** @var float - Units per packing */
private float $quantityInUnits = 0, 
    // TCMSFieldBoolean
/** @var bool - Virtual article */
private bool $virtualArticle = false, 
    // TCMSFieldBoolean
/** @var bool - Do not allow vouchers */
private bool $excludeFromVouchers = false, 
    // TCMSFieldBoolean
/** @var bool - Do not allow discounts for this article */
private bool $excludeFromDiscounts = false, 
    // TCMSFieldVarchar
/** @var string - Subtitle */
private string $subtitle = '', 
    // TCMSFieldBoolean
/** @var bool - Mark as new */
private bool $isNew = false, 
    // TCMSFieldNumber
/** @var int - Amount of pages */
private int $pages = 0, 
    // TCMSFieldVarchar
/** @var string - USP */
private string $usp = '', 
    // TCMSFieldBlob
/** @var string - Custom data */
private string $customData = '', 
    // TCMSFieldDecimal
/** @var float - Amount */
private float $orderAmount = 0, 
    // TCMSFieldDecimal
/** @var float - Total price */
private float $orderPriceTotal = 0, 
    // TCMSFieldDecimal
/** @var float - Order price after calculation of discounts */
private float $orderPriceAfterDiscounts = 0, 
    // TCMSFieldDecimal
/** @var float - Total weight (grams) */
private float $orderTotalWeight = 0, 
    // TCMSFieldDecimal
/** @var float - Total volume (cubic meters) */
private float $orderTotalVolume = 0, 
    // TCMSFieldDecimal
/** @var float - Unit price at time of order */
private float $orderPrice = 0, 
    // TCMSFieldBoolean
/** @var bool - Is a bundle */
private bool $isBundle = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderBundleArticle[] - Articles in order that belong to this bundle */
private \Doctrine\Common\Collections\Collection $shopOrderBundleArticleCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldDownloads
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] - Download file */
private \Doctrine\Common\Collections\Collection $download = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopOrder(): \ChameleonSystem\CoreBundle\Entity\ShopOrder|null
{
    return $this->shopOrder;
}
public function setShopOrder(\ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder): self
{
    $this->shopOrder = $shopOrder;
    $this->shopOrderId = $shopOrder?->getId();

    return $this;
}
public function getShopOrderId(): ?string
{
    return $this->shopOrderId;
}
public function setShopOrderId(?string $shopOrderId): self
{
    $this->shopOrderId = $shopOrderId;
    // todo - load new id
    //$this->shopOrderId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getBasketItemKey(): string
{
    return $this->basketItemKey;
}
public function setBasketItemKey(string $basketItemKey): self
{
    $this->basketItemKey = $basketItemKey;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopArticle(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->shopArticle;
}
public function setShopArticle(\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle): self
{
    $this->shopArticle = $shopArticle;
    $this->shopArticleId = $shopArticle?->getId();

    return $this;
}
public function getShopArticleId(): ?string
{
    return $this->shopArticleId;
}
public function setShopArticleId(?string $shopArticleId): self
{
    $this->shopArticleId = $shopArticleId;
    // todo - load new id
    //$this->shopArticleId = $?->getId();

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



  
    // TCMSFieldVarchar
public function getShopManufacturerName(): string
{
    return $this->shopManufacturerName;
}
public function setShopManufacturerName(string $shopManufacturerName): self
{
    $this->shopManufacturerName = $shopManufacturerName;

    return $this;
}


  
    // TCMSFieldDecimal
public function getPrice(): float
{
    return $this->price;
}
public function setPrice(float $price): self
{
    $this->price = $price;

    return $this;
}


  
    // TCMSFieldDecimal
public function getPriceReference(): float
{
    return $this->priceReference;
}
public function setPriceReference(float $priceReference): self
{
    $this->priceReference = $priceReference;

    return $this;
}


  
    // TCMSFieldDecimal
public function getPriceDiscounted(): float
{
    return $this->priceDiscounted;
}
public function setPriceDiscounted(float $priceDiscounted): self
{
    $this->priceDiscounted = $priceDiscounted;

    return $this;
}


  
    // TCMSFieldDecimal
public function getVatPercent(): float
{
    return $this->vatPercent;
}
public function setVatPercent(float $vatPercent): self
{
    $this->vatPercent = $vatPercent;

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


  
    // TCMSFieldNumber
public function getStock(): int
{
    return $this->stock;
}
public function setStock(int $stock): self
{
    $this->stock = $stock;

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


  
    // TCMSFieldNumber
public function getPages(): int
{
    return $this->pages;
}
public function setPages(int $pages): self
{
    $this->pages = $pages;

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


  
    // TCMSFieldBlob
public function getCustomData(): string
{
    return $this->customData;
}
public function setCustomData(string $customData): self
{
    $this->customData = $customData;

    return $this;
}


  
    // TCMSFieldDecimal
public function getOrderAmount(): float
{
    return $this->orderAmount;
}
public function setOrderAmount(float $orderAmount): self
{
    $this->orderAmount = $orderAmount;

    return $this;
}


  
    // TCMSFieldDecimal
public function getOrderPriceTotal(): float
{
    return $this->orderPriceTotal;
}
public function setOrderPriceTotal(float $orderPriceTotal): self
{
    $this->orderPriceTotal = $orderPriceTotal;

    return $this;
}


  
    // TCMSFieldDecimal
public function getOrderPriceAfterDiscounts(): float
{
    return $this->orderPriceAfterDiscounts;
}
public function setOrderPriceAfterDiscounts(float $orderPriceAfterDiscounts): self
{
    $this->orderPriceAfterDiscounts = $orderPriceAfterDiscounts;

    return $this;
}


  
    // TCMSFieldDecimal
public function getOrderTotalWeight(): float
{
    return $this->orderTotalWeight;
}
public function setOrderTotalWeight(float $orderTotalWeight): self
{
    $this->orderTotalWeight = $orderTotalWeight;

    return $this;
}


  
    // TCMSFieldDecimal
public function getOrderTotalVolume(): float
{
    return $this->orderTotalVolume;
}
public function setOrderTotalVolume(float $orderTotalVolume): self
{
    $this->orderTotalVolume = $orderTotalVolume;

    return $this;
}


  
    // TCMSFieldDecimal
public function getOrderPrice(): float
{
    return $this->orderPrice;
}
public function setOrderPrice(float $orderPrice): self
{
    $this->orderPrice = $orderPrice;

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
public function getShopOrderBundleArticleCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderBundleArticleCollection;
}
public function setShopOrderBundleArticleCollection(\Doctrine\Common\Collections\Collection $shopOrderBundleArticleCollection): self
{
    $this->shopOrderBundleArticleCollection = $shopOrderBundleArticleCollection;

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


  
}
