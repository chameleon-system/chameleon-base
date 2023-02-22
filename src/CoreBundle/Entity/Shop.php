<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\PkgShopCurrency;
use ChameleonSystem\CoreBundle\Entity\ShopCategory;
use ChameleonSystem\CoreBundle\Entity\TCountry;
use ChameleonSystem\CoreBundle\Entity\ShopBankAccount;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;
use ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby;
use ChameleonSystem\CoreBundle\Entity\ShopVat;
use ChameleonSystem\CoreBundle\Entity\ShopShippingGroup;
use ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation;
use ChameleonSystem\CoreBundle\Entity\DataCountry;
use ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate;
use ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize;
use ChameleonSystem\CoreBundle\Entity\ShopSystemInfo;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\ShopSearchLog;
use ChameleonSystem\CoreBundle\Entity\ShopSearchFieldWeight;
use ChameleonSystem\CoreBundle\Entity\ShopSearchIgnoreWord;
use ChameleonSystem\CoreBundle\Entity\ShopSearchKeywordArticle;
use ChameleonSystem\CoreBundle\Entity\ShopSearchCache;
use ChameleonSystem\CoreBundle\Entity\ShopStockMessage;
use ChameleonSystem\CoreBundle\Entity\PkgShopListfilter;
use ChameleonSystem\CoreBundle\Entity\PkgShopFooterCategory;

class Shop {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopOrderStatusCode> - Available shipping status codes */
private Collection $shopOrderStatusCodeCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var PkgShopCurrency|null - Default currency */
private ?PkgShopCurrency $defaultPkgShopCurrency = null
, 
    // TCMSFieldVarchar
/** @var string - Shop name */
private string $name = '', 
    // TCMSFieldLookup
/** @var ShopCategory|null - Shop main category */
private ?ShopCategory $shopCategory = null
, 
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
    // TCMSFieldLookup
/** @var TCountry|null - Company country */
private ?TCountry $tCountry = null
, 
    // TCMSFieldVarchar
/** @var string - Telephone (customer service) */
private string $customerServiceTelephone = '', 
    // TCMSFieldVarchar
/** @var string - Email (customer service) */
private string $customerServiceEmail = '', 
    // TCMSFieldVarchar
/** @var string - VAT registration number */
private string $shopvatnumber = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopBankAccount> - Bank accounts */
private Collection $shopBankAccountCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, DataExtranetUser> - Customers */
private Collection $dataExtranetUserCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Length of product history of an user */
private string $dataExtranetUserShopArticleHistoryMaxArticleCount = '20', 
    // TCMSFieldLookup
/** @var ShopModuleArticlelistOrderby|null - Default sorting of items in the category view */
private ?ShopModuleArticlelistOrderby $shopModuleArticlelistOrderby = null
, 
    // TCMSFieldLookup
/** @var ShopVat|null - Default VAT group */
private ?ShopVat $shopVat = null
, 
    // TCMSFieldLookup
/** @var ShopShippingGroup|null - Default shipping group */
private ?ShopShippingGroup $shopShippingGroup = null
, 
    // TCMSFieldLookup
/** @var DataExtranetSalutation|null - Default salutation */
private ?DataExtranetSalutation $dataExtranetSalutation = null
, 
    // TCMSFieldLookup
/** @var DataCountry|null - Default country */
private ?DataCountry $dataCountry = null
, 
    // TCMSFieldVarchar
/** @var string - Affiliate URL parameter */
private string $affiliateParameterName = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgShopAffiliate> - Affiliate programs */
private Collection $pkgShopAffiliateCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticleImageSize> - Size of product images */
private Collection $shopArticleImageSizeCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSystemInfo> - Shop specific information / text blocks (e.g. Terms and Conditions) */
private Collection $shopSystemInfoCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Replacement image */
private ?CmsMedia $notFoundIm = null
, 
    // TCMSFieldVarchar
/** @var string - Shortest searchable partial word */
private string $shopSearchMinIndexLength = '3', 
    // TCMSFieldVarchar
/** @var string - Longest searchable partial word */
private string $shopSearchMaxIndexLength = '10', 
    // TCMSFieldVarchar
/** @var string - Maximum age of search cache */
private string $maxSearchCacheAgeInHours = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSearchLog> - Search log */
private Collection $shopSearchLogCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSearchFieldWeight> - Fields weight */
private Collection $shopSearchFieldWeightCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSearchIgnoreWord> - Words to be ignored in searches */
private Collection $shopSearchIgnoreWordCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSearchKeywordArticle> - Manually selected search results */
private Collection $shopSearchKeywordArticleCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSearchCache> - Search cache */
private Collection $shopSearchCacheCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Name of the spot in the layouts containing the basket module */
private string $basketSpotName = '', 
    // TCMSFieldVarchar
/** @var string - Name of the spot containing the central shop handler */
private string $shopCentralHandlerSpotName = 'oShopCentralHandler', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopStockMessage> - Stock messages */
private Collection $shopStockMessageCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Export key */
private string $exportKey = '', 
    // TCMSFieldLookup
/** @var PkgShopListfilter|null - Results list filter */
private ?PkgShopListfilter $pkgShopListfilterPostsearch = null
, 
    // TCMSFieldLookup
/** @var PkgShopListfilter|null - Category list filter for categories without subcategories */
private ?PkgShopListfilter $pkgShopListfilterCategoryFilter = null
, 
    // TCMSFieldVarchar
/** @var string - Maximum size of cookie for item history (in KB) */
private string $dataExtranetUserShopArticleHistoryMaxCookieSize = '0', 
    // TCMSFieldVarchar
/** @var string - Shipping delay (days) */
private string $shopreviewmailMailDelay = '4', 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgShopFooterCategory> - Footer categories */
private Collection $pkgShopFooterCategoryCollection = new ArrayCollection()
  ) {}

  public function getId(): string
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
/**
* @return Collection<int, ShopOrderStatusCode>
*/
public function getShopOrderStatusCodeCollection(): Collection
{
    return $this->shopOrderStatusCodeCollection;
}

public function addShopOrderStatusCodeCollection(ShopOrderStatusCode $shopOrderStatusCode): self
{
    if (!$this->shopOrderStatusCodeCollection->contains($shopOrderStatusCode)) {
        $this->shopOrderStatusCodeCollection->add($shopOrderStatusCode);
        $shopOrderStatusCode->setShop($this);
    }

    return $this;
}

public function removeShopOrderStatusCodeCollection(ShopOrderStatusCode $shopOrderStatusCode): self
{
    if ($this->shopOrderStatusCodeCollection->removeElement($shopOrderStatusCode)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderStatusCode->getShop() === $this) {
            $shopOrderStatusCode->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
public function getDefaultPkgShopCurrency(): ?PkgShopCurrency
{
    return $this->defaultPkgShopCurrency;
}

public function setDefaultPkgShopCurrency(?PkgShopCurrency $defaultPkgShopCurrency): self
{
    $this->defaultPkgShopCurrency = $defaultPkgShopCurrency;

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


  
    // TCMSFieldLookup
public function getShopCategory(): ?ShopCategory
{
    return $this->shopCategory;
}

public function setShopCategory(?ShopCategory $shopCategory): self
{
    $this->shopCategory = $shopCategory;

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
public function getTCountry(): ?TCountry
{
    return $this->tCountry;
}

public function setTCountry(?TCountry $tCountry): self
{
    $this->tCountry = $tCountry;

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


  
    // TCMSFieldVarchar
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
/**
* @return Collection<int, ShopBankAccount>
*/
public function getShopBankAccountCollection(): Collection
{
    return $this->shopBankAccountCollection;
}

public function addShopBankAccountCollection(ShopBankAccount $shopBankAccount): self
{
    if (!$this->shopBankAccountCollection->contains($shopBankAccount)) {
        $this->shopBankAccountCollection->add($shopBankAccount);
        $shopBankAccount->setShop($this);
    }

    return $this;
}

public function removeShopBankAccountCollection(ShopBankAccount $shopBankAccount): self
{
    if ($this->shopBankAccountCollection->removeElement($shopBankAccount)) {
        // set the owning side to null (unless already changed)
        if ($shopBankAccount->getShop() === $this) {
            $shopBankAccount->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, DataExtranetUser>
*/
public function getDataExtranetUserCollection(): Collection
{
    return $this->dataExtranetUserCollection;
}

public function addDataExtranetUserCollection(DataExtranetUser $dataExtranetUser): self
{
    if (!$this->dataExtranetUserCollection->contains($dataExtranetUser)) {
        $this->dataExtranetUserCollection->add($dataExtranetUser);
        $dataExtranetUser->setShop($this);
    }

    return $this;
}

public function removeDataExtranetUserCollection(DataExtranetUser $dataExtranetUser): self
{
    if ($this->dataExtranetUserCollection->removeElement($dataExtranetUser)) {
        // set the owning side to null (unless already changed)
        if ($dataExtranetUser->getShop() === $this) {
            $dataExtranetUser->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getDataExtranetUserShopArticleHistoryMaxArticleCount(): string
{
    return $this->dataExtranetUserShopArticleHistoryMaxArticleCount;
}
public function setDataExtranetUserShopArticleHistoryMaxArticleCount(string $dataExtranetUserShopArticleHistoryMaxArticleCount): self
{
    $this->dataExtranetUserShopArticleHistoryMaxArticleCount = $dataExtranetUserShopArticleHistoryMaxArticleCount;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopModuleArticlelistOrderby(): ?ShopModuleArticlelistOrderby
{
    return $this->shopModuleArticlelistOrderby;
}

public function setShopModuleArticlelistOrderby(?ShopModuleArticlelistOrderby $shopModuleArticlelistOrderby): self
{
    $this->shopModuleArticlelistOrderby = $shopModuleArticlelistOrderby;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVat(): ?ShopVat
{
    return $this->shopVat;
}

public function setShopVat(?ShopVat $shopVat): self
{
    $this->shopVat = $shopVat;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopShippingGroup(): ?ShopShippingGroup
{
    return $this->shopShippingGroup;
}

public function setShopShippingGroup(?ShopShippingGroup $shopShippingGroup): self
{
    $this->shopShippingGroup = $shopShippingGroup;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataExtranetSalutation(): ?DataExtranetSalutation
{
    return $this->dataExtranetSalutation;
}

public function setDataExtranetSalutation(?DataExtranetSalutation $dataExtranetSalutation): self
{
    $this->dataExtranetSalutation = $dataExtranetSalutation;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataCountry(): ?DataCountry
{
    return $this->dataCountry;
}

public function setDataCountry(?DataCountry $dataCountry): self
{
    $this->dataCountry = $dataCountry;

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
/**
* @return Collection<int, PkgShopAffiliate>
*/
public function getPkgShopAffiliateCollection(): Collection
{
    return $this->pkgShopAffiliateCollection;
}

public function addPkgShopAffiliateCollection(PkgShopAffiliate $pkgShopAffiliate): self
{
    if (!$this->pkgShopAffiliateCollection->contains($pkgShopAffiliate)) {
        $this->pkgShopAffiliateCollection->add($pkgShopAffiliate);
        $pkgShopAffiliate->setShop($this);
    }

    return $this;
}

public function removePkgShopAffiliateCollection(PkgShopAffiliate $pkgShopAffiliate): self
{
    if ($this->pkgShopAffiliateCollection->removeElement($pkgShopAffiliate)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopAffiliate->getShop() === $this) {
            $pkgShopAffiliate->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopArticleImageSize>
*/
public function getShopArticleImageSizeCollection(): Collection
{
    return $this->shopArticleImageSizeCollection;
}

public function addShopArticleImageSizeCollection(ShopArticleImageSize $shopArticleImageSize): self
{
    if (!$this->shopArticleImageSizeCollection->contains($shopArticleImageSize)) {
        $this->shopArticleImageSizeCollection->add($shopArticleImageSize);
        $shopArticleImageSize->setShop($this);
    }

    return $this;
}

public function removeShopArticleImageSizeCollection(ShopArticleImageSize $shopArticleImageSize): self
{
    if ($this->shopArticleImageSizeCollection->removeElement($shopArticleImageSize)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleImageSize->getShop() === $this) {
            $shopArticleImageSize->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSystemInfo>
*/
public function getShopSystemInfoCollection(): Collection
{
    return $this->shopSystemInfoCollection;
}

public function addShopSystemInfoCollection(ShopSystemInfo $shopSystemInfo): self
{
    if (!$this->shopSystemInfoCollection->contains($shopSystemInfo)) {
        $this->shopSystemInfoCollection->add($shopSystemInfo);
        $shopSystemInfo->setShop($this);
    }

    return $this;
}

public function removeShopSystemInfoCollection(ShopSystemInfo $shopSystemInfo): self
{
    if ($this->shopSystemInfoCollection->removeElement($shopSystemInfo)) {
        // set the owning side to null (unless already changed)
        if ($shopSystemInfo->getShop() === $this) {
            $shopSystemInfo->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
public function getNotFoundIm(): ?CmsMedia
{
    return $this->notFoundIm;
}

public function setNotFoundIm(?CmsMedia $notFoundIm): self
{
    $this->notFoundIm = $notFoundIm;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShopSearchMinIndexLength(): string
{
    return $this->shopSearchMinIndexLength;
}
public function setShopSearchMinIndexLength(string $shopSearchMinIndexLength): self
{
    $this->shopSearchMinIndexLength = $shopSearchMinIndexLength;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShopSearchMaxIndexLength(): string
{
    return $this->shopSearchMaxIndexLength;
}
public function setShopSearchMaxIndexLength(string $shopSearchMaxIndexLength): self
{
    $this->shopSearchMaxIndexLength = $shopSearchMaxIndexLength;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMaxSearchCacheAgeInHours(): string
{
    return $this->maxSearchCacheAgeInHours;
}
public function setMaxSearchCacheAgeInHours(string $maxSearchCacheAgeInHours): self
{
    $this->maxSearchCacheAgeInHours = $maxSearchCacheAgeInHours;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSearchLog>
*/
public function getShopSearchLogCollection(): Collection
{
    return $this->shopSearchLogCollection;
}

public function addShopSearchLogCollection(ShopSearchLog $shopSearchLog): self
{
    if (!$this->shopSearchLogCollection->contains($shopSearchLog)) {
        $this->shopSearchLogCollection->add($shopSearchLog);
        $shopSearchLog->setShop($this);
    }

    return $this;
}

public function removeShopSearchLogCollection(ShopSearchLog $shopSearchLog): self
{
    if ($this->shopSearchLogCollection->removeElement($shopSearchLog)) {
        // set the owning side to null (unless already changed)
        if ($shopSearchLog->getShop() === $this) {
            $shopSearchLog->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSearchFieldWeight>
*/
public function getShopSearchFieldWeightCollection(): Collection
{
    return $this->shopSearchFieldWeightCollection;
}

public function addShopSearchFieldWeightCollection(ShopSearchFieldWeight $shopSearchFieldWeight): self
{
    if (!$this->shopSearchFieldWeightCollection->contains($shopSearchFieldWeight)) {
        $this->shopSearchFieldWeightCollection->add($shopSearchFieldWeight);
        $shopSearchFieldWeight->setShop($this);
    }

    return $this;
}

public function removeShopSearchFieldWeightCollection(ShopSearchFieldWeight $shopSearchFieldWeight): self
{
    if ($this->shopSearchFieldWeightCollection->removeElement($shopSearchFieldWeight)) {
        // set the owning side to null (unless already changed)
        if ($shopSearchFieldWeight->getShop() === $this) {
            $shopSearchFieldWeight->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSearchIgnoreWord>
*/
public function getShopSearchIgnoreWordCollection(): Collection
{
    return $this->shopSearchIgnoreWordCollection;
}

public function addShopSearchIgnoreWordCollection(ShopSearchIgnoreWord $shopSearchIgnoreWord): self
{
    if (!$this->shopSearchIgnoreWordCollection->contains($shopSearchIgnoreWord)) {
        $this->shopSearchIgnoreWordCollection->add($shopSearchIgnoreWord);
        $shopSearchIgnoreWord->setShop($this);
    }

    return $this;
}

public function removeShopSearchIgnoreWordCollection(ShopSearchIgnoreWord $shopSearchIgnoreWord): self
{
    if ($this->shopSearchIgnoreWordCollection->removeElement($shopSearchIgnoreWord)) {
        // set the owning side to null (unless already changed)
        if ($shopSearchIgnoreWord->getShop() === $this) {
            $shopSearchIgnoreWord->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSearchKeywordArticle>
*/
public function getShopSearchKeywordArticleCollection(): Collection
{
    return $this->shopSearchKeywordArticleCollection;
}

public function addShopSearchKeywordArticleCollection(ShopSearchKeywordArticle $shopSearchKeywordArticle): self
{
    if (!$this->shopSearchKeywordArticleCollection->contains($shopSearchKeywordArticle)) {
        $this->shopSearchKeywordArticleCollection->add($shopSearchKeywordArticle);
        $shopSearchKeywordArticle->setShop($this);
    }

    return $this;
}

public function removeShopSearchKeywordArticleCollection(ShopSearchKeywordArticle $shopSearchKeywordArticle): self
{
    if ($this->shopSearchKeywordArticleCollection->removeElement($shopSearchKeywordArticle)) {
        // set the owning side to null (unless already changed)
        if ($shopSearchKeywordArticle->getShop() === $this) {
            $shopSearchKeywordArticle->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSearchCache>
*/
public function getShopSearchCacheCollection(): Collection
{
    return $this->shopSearchCacheCollection;
}

public function addShopSearchCacheCollection(ShopSearchCache $shopSearchCache): self
{
    if (!$this->shopSearchCacheCollection->contains($shopSearchCache)) {
        $this->shopSearchCacheCollection->add($shopSearchCache);
        $shopSearchCache->setShop($this);
    }

    return $this;
}

public function removeShopSearchCacheCollection(ShopSearchCache $shopSearchCache): self
{
    if ($this->shopSearchCacheCollection->removeElement($shopSearchCache)) {
        // set the owning side to null (unless already changed)
        if ($shopSearchCache->getShop() === $this) {
            $shopSearchCache->setShop(null);
        }
    }

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopStockMessage>
*/
public function getShopStockMessageCollection(): Collection
{
    return $this->shopStockMessageCollection;
}

public function addShopStockMessageCollection(ShopStockMessage $shopStockMessage): self
{
    if (!$this->shopStockMessageCollection->contains($shopStockMessage)) {
        $this->shopStockMessageCollection->add($shopStockMessage);
        $shopStockMessage->setShop($this);
    }

    return $this;
}

public function removeShopStockMessageCollection(ShopStockMessage $shopStockMessage): self
{
    if ($this->shopStockMessageCollection->removeElement($shopStockMessage)) {
        // set the owning side to null (unless already changed)
        if ($shopStockMessage->getShop() === $this) {
            $shopStockMessage->setShop(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getExportKey(): string
{
    return $this->exportKey;
}
public function setExportKey(string $exportKey): self
{
    $this->exportKey = $exportKey;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopListfilterPostsearch(): ?PkgShopListfilter
{
    return $this->pkgShopListfilterPostsearch;
}

public function setPkgShopListfilterPostsearch(?PkgShopListfilter $pkgShopListfilterPostsearch): self
{
    $this->pkgShopListfilterPostsearch = $pkgShopListfilterPostsearch;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopListfilterCategoryFilter(): ?PkgShopListfilter
{
    return $this->pkgShopListfilterCategoryFilter;
}

public function setPkgShopListfilterCategoryFilter(?PkgShopListfilter $pkgShopListfilterCategoryFilter): self
{
    $this->pkgShopListfilterCategoryFilter = $pkgShopListfilterCategoryFilter;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDataExtranetUserShopArticleHistoryMaxCookieSize(): string
{
    return $this->dataExtranetUserShopArticleHistoryMaxCookieSize;
}
public function setDataExtranetUserShopArticleHistoryMaxCookieSize(string $dataExtranetUserShopArticleHistoryMaxCookieSize): self
{
    $this->dataExtranetUserShopArticleHistoryMaxCookieSize = $dataExtranetUserShopArticleHistoryMaxCookieSize;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShopreviewmailMailDelay(): string
{
    return $this->shopreviewmailMailDelay;
}
public function setShopreviewmailMailDelay(string $shopreviewmailMailDelay): self
{
    $this->shopreviewmailMailDelay = $shopreviewmailMailDelay;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, PkgShopFooterCategory>
*/
public function getPkgShopFooterCategoryCollection(): Collection
{
    return $this->pkgShopFooterCategoryCollection;
}

public function addPkgShopFooterCategoryCollection(PkgShopFooterCategory $pkgShopFooterCategory): self
{
    if (!$this->pkgShopFooterCategoryCollection->contains($pkgShopFooterCategory)) {
        $this->pkgShopFooterCategoryCollection->add($pkgShopFooterCategory);
        $pkgShopFooterCategory->setShop($this);
    }

    return $this;
}

public function removePkgShopFooterCategoryCollection(PkgShopFooterCategory $pkgShopFooterCategory): self
{
    if ($this->pkgShopFooterCategoryCollection->removeElement($pkgShopFooterCategory)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopFooterCategory->getShop() === $this) {
            $pkgShopFooterCategory->setShop(null);
        }
    }

    return $this;
}


  
}
