<?php
namespace ChameleonSystem\CoreBundle\Entity;

class Shop {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null - Default currency */
private \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null $defaultPkgShopCurrency = null,
/** @var null|string - Default currency */
private ?string $defaultPkgShopCurrencyId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory|null - Shop main category */
private \ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory = null,
/** @var null|string - Shop main category */
private ?string $shopCategoryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\TCountry|null - Company country */
private \ChameleonSystem\CoreBundle\Entity\TCountry|null $tCountry = null,
/** @var null|string - Company country */
private ?string $tCountryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null - Default sorting of items in the category view */
private \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null $shopModuleArticlelistOrderby = null,
/** @var null|string - Default sorting of items in the category view */
private ?string $shopModuleArticlelistOrderbyId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVat|null - Default VAT group */
private \ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat = null,
/** @var null|string - Default VAT group */
private ?string $shopVatId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup|null - Default shipping group */
private \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup|null $shopShippingGroup = null,
/** @var null|string - Default shipping group */
private ?string $shopShippingGroupId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null - Default salutation */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $dataExtranetSalutation = null,
/** @var null|string - Default salutation */
private ?string $dataExtranetSalutationId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry|null - Default country */
private \ChameleonSystem\CoreBundle\Entity\DataCountry|null $dataCountry = null,
/** @var null|string - Default country */
private ?string $dataCountryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Replacement image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $notFoundImage = null,
/** @var null|string - Replacement image */
private ?string $notFoundImageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null - Results list filter */
private \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilterPostsearch = null,
/** @var null|string - Results list filter */
private ?string $pkgShopListfilterPostsearchId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null - Category list filter for categories without subcategories */
private \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilterCategoryFilter = null,
/** @var null|string - Category list filter for categories without subcategories */
private ?string $pkgShopListfilterCategoryFilterId = null
, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode[] - Available shipping status codes */
private \Doctrine\Common\Collections\Collection $shopOrderStatusCodeCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Shop name */
private string $name = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - Belongs to these portals */
private \Doctrine\Common\Collections\Collection $cmsPortalMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Company name */
private string $adrCompany = '', 
    // TCMSFieldVarchar
/** @var string - Company street */
private string $adrStreet = '', 
    // TCMSFieldVarchar
/** @var string - Company zip code */
private string $adrZip = '', 
    // TCMSFieldVarchar
/** @var string - Company city */
private string $adrCity = '', 
    // TCMSFieldVarchar
/** @var string - Telephone (customer service) */
private string $customerServiceTelephone = '', 
    // TCMSFieldEmail
/** @var string - Email (customer service) */
private string $customerServiceEmail = '', 
    // TCMSFieldVarchar
/** @var string - VAT registration number */
private string $shopvatnumber = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopBankAccount[] - Bank accounts */
private \Doctrine\Common\Collections\Collection $shopBankAccountCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] - Customers */
private \Doctrine\Common\Collections\Collection $dataExtranetUserCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Length of product history of an user */
private int $dataExtranetUserShopArticleHistoryMaxArticleCount = 20, 
    // TCMSFieldBoolean
/** @var bool - Make VAT of shipping costs dependent on basket contents */
private bool $shippingVatDependsOnBasketContents = true, 
    // TCMSFieldVarchar
/** @var string - Affiliate URL parameter */
private string $affiliateParameterName = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate[] - Affiliate programs */
private \Doctrine\Common\Collections\Collection $pkgShopAffiliateCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize[] - Size of product images */
private \Doctrine\Common\Collections\Collection $shopArticleImageSizeCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSystemInfo[] - Shop specific information / text blocks (e.g. Terms and Conditions) */
private \Doctrine\Common\Collections\Collection $shopSystemInfoCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldDecimal
/** @var float - Weight bonus for whole words in search */
private float $shopSearchWordBonus = 0, 
    // TCMSFieldDecimal
/** @var float - Weight of search word length */
private float $shopSearchWordLengthFactor = 0.8, 
    // TCMSFieldDecimal
/** @var float - Deduction for words that only sound similar */
private float $shopSearchSoundexPenalty = 0, 
    // TCMSFieldNumber
/** @var int - Shortest searchable partial word */
private int $shopSearchMinIndexLength = 3, 
    // TCMSFieldNumber
/** @var int - Longest searchable partial word */
private int $shopSearchMaxIndexLength = 10, 
    // TCMSFieldBoolean
/** @var bool - Connect search items with AND */
private bool $shopSearchUseBooleanAnd = false, 
    // TCMSFieldNumber
/** @var int - Maximum age of search cache */
private int $maxSearchCacheAgeInHours = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchLog[] - Search log */
private \Doctrine\Common\Collections\Collection $shopSearchLogCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchFieldWeight[] - Fields weight */
private \Doctrine\Common\Collections\Collection $shopSearchFieldWeightCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchIgnoreWord[] - Words to be ignored in searches */
private \Doctrine\Common\Collections\Collection $shopSearchIgnoreWordCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchKeywordArticle[] - Manually selected search results */
private \Doctrine\Common\Collections\Collection $shopSearchKeywordArticleCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchCache[] - Search cache */
private \Doctrine\Common\Collections\Collection $shopSearchCacheCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Name of the spot in the layouts containing the basket module */
private string $basketSpotName = '', 
    // TCMSFieldVarchar
/** @var string - Name of the spot containing the central shop handler */
private string $shopCentralHandlerSpotName = 'oShopCentralHandler', 
    // TCMSFieldBoolean
/** @var bool - Show empty categories in shop */
private bool $showEmptyCategories = true, 
    // TCMSFieldBoolean
/** @var bool - Variant parents can be purchased */
private bool $allowPurchaseOfVariantParents = false, 
    // TCMSFieldBoolean
/** @var bool - Load inactive variants */
private bool $loadInactiveVariants = false, 
    // TCMSFieldBoolean
/** @var bool - Synchronize profile address with billing address */
private bool $syncProfileDataWithBillingData = false, 
    // TCMSFieldBoolean
/** @var bool - Is the user allowed to have more than one billing address? */
private bool $allowMultipleBillingAddresses = true, 
    // TCMSFieldBoolean
/** @var bool - Is the user allowed to have more than one shipping address? */
private bool $allowMultipleShippingAddresses = true, 
    // TCMSFieldBoolean
/** @var bool - Allow guest orders? */
private bool $allowGuestPurchase = true, 
    // TCMSFieldBoolean
/** @var bool - Archive customers product recommendations */
private bool $logArticleSuggestions = true, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopStockMessage[] - Stock messages */
private \Doctrine\Common\Collections\Collection $shopStockMessageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText_ShowExportURL
/** @var string - Export key */
private string $exportKey = '', 
    // TCMSFieldText
/** @var string - Basket info text */
private string $cartInfoText = '', 
    // TCMSFieldBoolean
/** @var bool - If there are no results, refer to page "no results for product search" */
private bool $redirectToNotFoundPageProductSearchOnNoResults = false, 
    // TCMSFieldBoolean
/** @var bool - Turn on search log */
private bool $useShopSearchLog = true, 
    // TCMSFieldNumber
/** @var int - Maximum size of cookie for item history (in KB) */
private int $dataExtranetUserShopArticleHistoryMaxCookieSize = 0, 
    // TCMSFieldOption
/** @var string - Use SEO-URLs for products */
private string $productUrlMode = 'V1', 
    // TCMSFieldNumber
/** @var int - Shipping delay (days) */
private int $shopreviewmailMailDelay = 4, 
    // TCMSFieldDecimal
/** @var float - Recipients (percent) */
private float $shopreviewmailPercentOfCustomers = 90, 
    // TCMSFieldBoolean
/** @var bool - For each order */
private bool $shopreviewmailSendForEachOrder = true, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopFooterCategory[] - Footer categories */
private \Doctrine\Common\Collections\Collection $pkgShopFooterCategoryCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
    // TCMSFieldPropertyTable
public function getShopOrderStatusCodeCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderStatusCodeCollection;
}
public function setShopOrderStatusCodeCollection(\Doctrine\Common\Collections\Collection $shopOrderStatusCodeCollection): self
{
    $this->shopOrderStatusCodeCollection = $shopOrderStatusCodeCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getDefaultPkgShopCurrency(): \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null
{
    return $this->defaultPkgShopCurrency;
}
public function setDefaultPkgShopCurrency(\ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null $defaultPkgShopCurrency): self
{
    $this->defaultPkgShopCurrency = $defaultPkgShopCurrency;
    $this->defaultPkgShopCurrencyId = $defaultPkgShopCurrency?->getId();

    return $this;
}
public function getDefaultPkgShopCurrencyId(): ?string
{
    return $this->defaultPkgShopCurrencyId;
}
public function setDefaultPkgShopCurrencyId(?string $defaultPkgShopCurrencyId): self
{
    $this->defaultPkgShopCurrencyId = $defaultPkgShopCurrencyId;
    // todo - load new id
    //$this->defaultPkgShopCurrencyId = $?->getId();

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


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsPortalMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalMlt;
}
public function setCmsPortalMlt(\Doctrine\Common\Collections\Collection $cmsPortalMlt): self
{
    $this->cmsPortalMlt = $cmsPortalMlt;

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



  
    // TCMSFieldVarchar
public function getAdrCompany(): string
{
    return $this->adrCompany;
}
public function setAdrCompany(string $adrCompany): self
{
    $this->adrCompany = $adrCompany;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrStreet(): string
{
    return $this->adrStreet;
}
public function setAdrStreet(string $adrStreet): self
{
    $this->adrStreet = $adrStreet;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrZip(): string
{
    return $this->adrZip;
}
public function setAdrZip(string $adrZip): self
{
    $this->adrZip = $adrZip;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrCity(): string
{
    return $this->adrCity;
}
public function setAdrCity(string $adrCity): self
{
    $this->adrCity = $adrCity;

    return $this;
}


  
    // TCMSFieldLookup
public function getTCountry(): \ChameleonSystem\CoreBundle\Entity\TCountry|null
{
    return $this->tCountry;
}
public function setTCountry(\ChameleonSystem\CoreBundle\Entity\TCountry|null $tCountry): self
{
    $this->tCountry = $tCountry;
    $this->tCountryId = $tCountry?->getId();

    return $this;
}
public function getTCountryId(): ?string
{
    return $this->tCountryId;
}
public function setTCountryId(?string $tCountryId): self
{
    $this->tCountryId = $tCountryId;
    // todo - load new id
    //$this->tCountryId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getCustomerServiceTelephone(): string
{
    return $this->customerServiceTelephone;
}
public function setCustomerServiceTelephone(string $customerServiceTelephone): self
{
    $this->customerServiceTelephone = $customerServiceTelephone;

    return $this;
}


  
    // TCMSFieldEmail
public function getCustomerServiceEmail(): string
{
    return $this->customerServiceEmail;
}
public function setCustomerServiceEmail(string $customerServiceEmail): self
{
    $this->customerServiceEmail = $customerServiceEmail;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShopvatnumber(): string
{
    return $this->shopvatnumber;
}
public function setShopvatnumber(string $shopvatnumber): self
{
    $this->shopvatnumber = $shopvatnumber;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopBankAccountCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopBankAccountCollection;
}
public function setShopBankAccountCollection(\Doctrine\Common\Collections\Collection $shopBankAccountCollection): self
{
    $this->shopBankAccountCollection = $shopBankAccountCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getDataExtranetUserCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetUserCollection;
}
public function setDataExtranetUserCollection(\Doctrine\Common\Collections\Collection $dataExtranetUserCollection): self
{
    $this->dataExtranetUserCollection = $dataExtranetUserCollection;

    return $this;
}


  
    // TCMSFieldNumber
public function getDataExtranetUserShopArticleHistoryMaxArticleCount(): int
{
    return $this->dataExtranetUserShopArticleHistoryMaxArticleCount;
}
public function setDataExtranetUserShopArticleHistoryMaxArticleCount(int $dataExtranetUserShopArticleHistoryMaxArticleCount): self
{
    $this->dataExtranetUserShopArticleHistoryMaxArticleCount = $dataExtranetUserShopArticleHistoryMaxArticleCount;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopModuleArticlelistOrderby(): \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null
{
    return $this->shopModuleArticlelistOrderby;
}
public function setShopModuleArticlelistOrderby(\ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null $shopModuleArticlelistOrderby): self
{
    $this->shopModuleArticlelistOrderby = $shopModuleArticlelistOrderby;
    $this->shopModuleArticlelistOrderbyId = $shopModuleArticlelistOrderby?->getId();

    return $this;
}
public function getShopModuleArticlelistOrderbyId(): ?string
{
    return $this->shopModuleArticlelistOrderbyId;
}
public function setShopModuleArticlelistOrderbyId(?string $shopModuleArticlelistOrderbyId): self
{
    $this->shopModuleArticlelistOrderbyId = $shopModuleArticlelistOrderbyId;
    // todo - load new id
    //$this->shopModuleArticlelistOrderbyId = $?->getId();

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



  
    // TCMSFieldLookup
public function getShopShippingGroup(): \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup|null
{
    return $this->shopShippingGroup;
}
public function setShopShippingGroup(\ChameleonSystem\CoreBundle\Entity\ShopShippingGroup|null $shopShippingGroup): self
{
    $this->shopShippingGroup = $shopShippingGroup;
    $this->shopShippingGroupId = $shopShippingGroup?->getId();

    return $this;
}
public function getShopShippingGroupId(): ?string
{
    return $this->shopShippingGroupId;
}
public function setShopShippingGroupId(?string $shopShippingGroupId): self
{
    $this->shopShippingGroupId = $shopShippingGroupId;
    // todo - load new id
    //$this->shopShippingGroupId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isShippingVatDependsOnBasketContents(): bool
{
    return $this->shippingVatDependsOnBasketContents;
}
public function setShippingVatDependsOnBasketContents(bool $shippingVatDependsOnBasketContents): self
{
    $this->shippingVatDependsOnBasketContents = $shippingVatDependsOnBasketContents;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataExtranetSalutation(): \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null
{
    return $this->dataExtranetSalutation;
}
public function setDataExtranetSalutation(\ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $dataExtranetSalutation): self
{
    $this->dataExtranetSalutation = $dataExtranetSalutation;
    $this->dataExtranetSalutationId = $dataExtranetSalutation?->getId();

    return $this;
}
public function getDataExtranetSalutationId(): ?string
{
    return $this->dataExtranetSalutationId;
}
public function setDataExtranetSalutationId(?string $dataExtranetSalutationId): self
{
    $this->dataExtranetSalutationId = $dataExtranetSalutationId;
    // todo - load new id
    //$this->dataExtranetSalutationId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getDataCountry(): \ChameleonSystem\CoreBundle\Entity\DataCountry|null
{
    return $this->dataCountry;
}
public function setDataCountry(\ChameleonSystem\CoreBundle\Entity\DataCountry|null $dataCountry): self
{
    $this->dataCountry = $dataCountry;
    $this->dataCountryId = $dataCountry?->getId();

    return $this;
}
public function getDataCountryId(): ?string
{
    return $this->dataCountryId;
}
public function setDataCountryId(?string $dataCountryId): self
{
    $this->dataCountryId = $dataCountryId;
    // todo - load new id
    //$this->dataCountryId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getAffiliateParameterName(): string
{
    return $this->affiliateParameterName;
}
public function setAffiliateParameterName(string $affiliateParameterName): self
{
    $this->affiliateParameterName = $affiliateParameterName;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopAffiliateCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopAffiliateCollection;
}
public function setPkgShopAffiliateCollection(\Doctrine\Common\Collections\Collection $pkgShopAffiliateCollection): self
{
    $this->pkgShopAffiliateCollection = $pkgShopAffiliateCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopArticleImageSizeCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleImageSizeCollection;
}
public function setShopArticleImageSizeCollection(\Doctrine\Common\Collections\Collection $shopArticleImageSizeCollection): self
{
    $this->shopArticleImageSizeCollection = $shopArticleImageSizeCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopSystemInfoCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSystemInfoCollection;
}
public function setShopSystemInfoCollection(\Doctrine\Common\Collections\Collection $shopSystemInfoCollection): self
{
    $this->shopSystemInfoCollection = $shopSystemInfoCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getNotFoundImage(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->notFoundImage;
}
public function setNotFoundImage(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $notFoundImage): self
{
    $this->notFoundImage = $notFoundImage;
    $this->notFoundImageId = $notFoundImage?->getId();

    return $this;
}
public function getNotFoundImageId(): ?string
{
    return $this->notFoundImageId;
}
public function setNotFoundImageId(?string $notFoundImageId): self
{
    $this->notFoundImageId = $notFoundImageId;
    // todo - load new id
    //$this->notFoundImageId = $?->getId();

    return $this;
}



  
    // TCMSFieldDecimal
public function getShopSearchWordBonus(): float
{
    return $this->shopSearchWordBonus;
}
public function setShopSearchWordBonus(float $shopSearchWordBonus): self
{
    $this->shopSearchWordBonus = $shopSearchWordBonus;

    return $this;
}


  
    // TCMSFieldDecimal
public function getShopSearchWordLengthFactor(): float
{
    return $this->shopSearchWordLengthFactor;
}
public function setShopSearchWordLengthFactor(float $shopSearchWordLengthFactor): self
{
    $this->shopSearchWordLengthFactor = $shopSearchWordLengthFactor;

    return $this;
}


  
    // TCMSFieldDecimal
public function getShopSearchSoundexPenalty(): float
{
    return $this->shopSearchSoundexPenalty;
}
public function setShopSearchSoundexPenalty(float $shopSearchSoundexPenalty): self
{
    $this->shopSearchSoundexPenalty = $shopSearchSoundexPenalty;

    return $this;
}


  
    // TCMSFieldNumber
public function getShopSearchMinIndexLength(): int
{
    return $this->shopSearchMinIndexLength;
}
public function setShopSearchMinIndexLength(int $shopSearchMinIndexLength): self
{
    $this->shopSearchMinIndexLength = $shopSearchMinIndexLength;

    return $this;
}


  
    // TCMSFieldNumber
public function getShopSearchMaxIndexLength(): int
{
    return $this->shopSearchMaxIndexLength;
}
public function setShopSearchMaxIndexLength(int $shopSearchMaxIndexLength): self
{
    $this->shopSearchMaxIndexLength = $shopSearchMaxIndexLength;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShopSearchUseBooleanAnd(): bool
{
    return $this->shopSearchUseBooleanAnd;
}
public function setShopSearchUseBooleanAnd(bool $shopSearchUseBooleanAnd): self
{
    $this->shopSearchUseBooleanAnd = $shopSearchUseBooleanAnd;

    return $this;
}


  
    // TCMSFieldNumber
public function getMaxSearchCacheAgeInHours(): int
{
    return $this->maxSearchCacheAgeInHours;
}
public function setMaxSearchCacheAgeInHours(int $maxSearchCacheAgeInHours): self
{
    $this->maxSearchCacheAgeInHours = $maxSearchCacheAgeInHours;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopSearchLogCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSearchLogCollection;
}
public function setShopSearchLogCollection(\Doctrine\Common\Collections\Collection $shopSearchLogCollection): self
{
    $this->shopSearchLogCollection = $shopSearchLogCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopSearchFieldWeightCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSearchFieldWeightCollection;
}
public function setShopSearchFieldWeightCollection(\Doctrine\Common\Collections\Collection $shopSearchFieldWeightCollection): self
{
    $this->shopSearchFieldWeightCollection = $shopSearchFieldWeightCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopSearchIgnoreWordCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSearchIgnoreWordCollection;
}
public function setShopSearchIgnoreWordCollection(\Doctrine\Common\Collections\Collection $shopSearchIgnoreWordCollection): self
{
    $this->shopSearchIgnoreWordCollection = $shopSearchIgnoreWordCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopSearchKeywordArticleCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSearchKeywordArticleCollection;
}
public function setShopSearchKeywordArticleCollection(\Doctrine\Common\Collections\Collection $shopSearchKeywordArticleCollection): self
{
    $this->shopSearchKeywordArticleCollection = $shopSearchKeywordArticleCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopSearchCacheCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSearchCacheCollection;
}
public function setShopSearchCacheCollection(\Doctrine\Common\Collections\Collection $shopSearchCacheCollection): self
{
    $this->shopSearchCacheCollection = $shopSearchCacheCollection;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBasketSpotName(): string
{
    return $this->basketSpotName;
}
public function setBasketSpotName(string $basketSpotName): self
{
    $this->basketSpotName = $basketSpotName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShopCentralHandlerSpotName(): string
{
    return $this->shopCentralHandlerSpotName;
}
public function setShopCentralHandlerSpotName(string $shopCentralHandlerSpotName): self
{
    $this->shopCentralHandlerSpotName = $shopCentralHandlerSpotName;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowEmptyCategories(): bool
{
    return $this->showEmptyCategories;
}
public function setShowEmptyCategories(bool $showEmptyCategories): self
{
    $this->showEmptyCategories = $showEmptyCategories;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowPurchaseOfVariantParents(): bool
{
    return $this->allowPurchaseOfVariantParents;
}
public function setAllowPurchaseOfVariantParents(bool $allowPurchaseOfVariantParents): self
{
    $this->allowPurchaseOfVariantParents = $allowPurchaseOfVariantParents;

    return $this;
}


  
    // TCMSFieldBoolean
public function isLoadInactiveVariants(): bool
{
    return $this->loadInactiveVariants;
}
public function setLoadInactiveVariants(bool $loadInactiveVariants): self
{
    $this->loadInactiveVariants = $loadInactiveVariants;

    return $this;
}


  
    // TCMSFieldBoolean
public function isSyncProfileDataWithBillingData(): bool
{
    return $this->syncProfileDataWithBillingData;
}
public function setSyncProfileDataWithBillingData(bool $syncProfileDataWithBillingData): self
{
    $this->syncProfileDataWithBillingData = $syncProfileDataWithBillingData;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowMultipleBillingAddresses(): bool
{
    return $this->allowMultipleBillingAddresses;
}
public function setAllowMultipleBillingAddresses(bool $allowMultipleBillingAddresses): self
{
    $this->allowMultipleBillingAddresses = $allowMultipleBillingAddresses;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowMultipleShippingAddresses(): bool
{
    return $this->allowMultipleShippingAddresses;
}
public function setAllowMultipleShippingAddresses(bool $allowMultipleShippingAddresses): self
{
    $this->allowMultipleShippingAddresses = $allowMultipleShippingAddresses;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowGuestPurchase(): bool
{
    return $this->allowGuestPurchase;
}
public function setAllowGuestPurchase(bool $allowGuestPurchase): self
{
    $this->allowGuestPurchase = $allowGuestPurchase;

    return $this;
}


  
    // TCMSFieldBoolean
public function isLogArticleSuggestions(): bool
{
    return $this->logArticleSuggestions;
}
public function setLogArticleSuggestions(bool $logArticleSuggestions): self
{
    $this->logArticleSuggestions = $logArticleSuggestions;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopStockMessageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopStockMessageCollection;
}
public function setShopStockMessageCollection(\Doctrine\Common\Collections\Collection $shopStockMessageCollection): self
{
    $this->shopStockMessageCollection = $shopStockMessageCollection;

    return $this;
}


  
    // TCMSFieldText_ShowExportURL
public function getExportKey(): string
{
    return $this->exportKey;
}
public function setExportKey(string $exportKey): self
{
    $this->exportKey = $exportKey;

    return $this;
}


  
    // TCMSFieldText
public function getCartInfoText(): string
{
    return $this->cartInfoText;
}
public function setCartInfoText(string $cartInfoText): self
{
    $this->cartInfoText = $cartInfoText;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopListfilterPostsearch(): \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null
{
    return $this->pkgShopListfilterPostsearch;
}
public function setPkgShopListfilterPostsearch(\ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilterPostsearch): self
{
    $this->pkgShopListfilterPostsearch = $pkgShopListfilterPostsearch;
    $this->pkgShopListfilterPostsearchId = $pkgShopListfilterPostsearch?->getId();

    return $this;
}
public function getPkgShopListfilterPostsearchId(): ?string
{
    return $this->pkgShopListfilterPostsearchId;
}
public function setPkgShopListfilterPostsearchId(?string $pkgShopListfilterPostsearchId): self
{
    $this->pkgShopListfilterPostsearchId = $pkgShopListfilterPostsearchId;
    // todo - load new id
    //$this->pkgShopListfilterPostsearchId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isRedirectToNotFoundPageProductSearchOnNoResults(): bool
{
    return $this->redirectToNotFoundPageProductSearchOnNoResults;
}
public function setRedirectToNotFoundPageProductSearchOnNoResults(bool $redirectToNotFoundPageProductSearchOnNoResults): self
{
    $this->redirectToNotFoundPageProductSearchOnNoResults = $redirectToNotFoundPageProductSearchOnNoResults;

    return $this;
}


  
    // TCMSFieldBoolean
public function isUseShopSearchLog(): bool
{
    return $this->useShopSearchLog;
}
public function setUseShopSearchLog(bool $useShopSearchLog): self
{
    $this->useShopSearchLog = $useShopSearchLog;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopListfilterCategoryFilter(): \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null
{
    return $this->pkgShopListfilterCategoryFilter;
}
public function setPkgShopListfilterCategoryFilter(\ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilterCategoryFilter): self
{
    $this->pkgShopListfilterCategoryFilter = $pkgShopListfilterCategoryFilter;
    $this->pkgShopListfilterCategoryFilterId = $pkgShopListfilterCategoryFilter?->getId();

    return $this;
}
public function getPkgShopListfilterCategoryFilterId(): ?string
{
    return $this->pkgShopListfilterCategoryFilterId;
}
public function setPkgShopListfilterCategoryFilterId(?string $pkgShopListfilterCategoryFilterId): self
{
    $this->pkgShopListfilterCategoryFilterId = $pkgShopListfilterCategoryFilterId;
    // todo - load new id
    //$this->pkgShopListfilterCategoryFilterId = $?->getId();

    return $this;
}



  
    // TCMSFieldNumber
public function getDataExtranetUserShopArticleHistoryMaxCookieSize(): int
{
    return $this->dataExtranetUserShopArticleHistoryMaxCookieSize;
}
public function setDataExtranetUserShopArticleHistoryMaxCookieSize(int $dataExtranetUserShopArticleHistoryMaxCookieSize): self
{
    $this->dataExtranetUserShopArticleHistoryMaxCookieSize = $dataExtranetUserShopArticleHistoryMaxCookieSize;

    return $this;
}


  
    // TCMSFieldOption
public function getProductUrlMode(): string
{
    return $this->productUrlMode;
}
public function setProductUrlMode(string $productUrlMode): self
{
    $this->productUrlMode = $productUrlMode;

    return $this;
}


  
    // TCMSFieldNumber
public function getShopreviewmailMailDelay(): int
{
    return $this->shopreviewmailMailDelay;
}
public function setShopreviewmailMailDelay(int $shopreviewmailMailDelay): self
{
    $this->shopreviewmailMailDelay = $shopreviewmailMailDelay;

    return $this;
}


  
    // TCMSFieldDecimal
public function getShopreviewmailPercentOfCustomers(): float
{
    return $this->shopreviewmailPercentOfCustomers;
}
public function setShopreviewmailPercentOfCustomers(float $shopreviewmailPercentOfCustomers): self
{
    $this->shopreviewmailPercentOfCustomers = $shopreviewmailPercentOfCustomers;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShopreviewmailSendForEachOrder(): bool
{
    return $this->shopreviewmailSendForEachOrder;
}
public function setShopreviewmailSendForEachOrder(bool $shopreviewmailSendForEachOrder): self
{
    $this->shopreviewmailSendForEachOrder = $shopreviewmailSendForEachOrder;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopFooterCategoryCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopFooterCategoryCollection;
}
public function setPkgShopFooterCategoryCollection(\Doctrine\Common\Collections\Collection $pkgShopFooterCategoryCollection): self
{
    $this->pkgShopFooterCategoryCollection = $pkgShopFooterCategoryCollection;

    return $this;
}


  
}
