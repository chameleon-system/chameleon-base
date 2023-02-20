<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\ShopBankAccount;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;
use ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate;
use ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize;
use ChameleonSystem\CoreBundle\Entity\ShopSystemInfo;
use ChameleonSystem\CoreBundle\Entity\ShopSearchLog;
use ChameleonSystem\CoreBundle\Entity\ShopSearchFieldWeight;
use ChameleonSystem\CoreBundle\Entity\ShopSearchIgnoreWord;
use ChameleonSystem\CoreBundle\Entity\ShopSearchKeywordArticle;
use ChameleonSystem\CoreBundle\Entity\ShopSearchCache;
use ChameleonSystem\CoreBundle\Entity\ShopStockMessage;
use ChameleonSystem\CoreBundle\Entity\PkgShopFooterCategory;

class Shop {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderStatusCode> - Available shipping status codes */
private Collection $shopOrderStatusCodeCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Shop name */
private string $name = '', 
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
    // TCMSFieldVarchar
/** @var string - Email (customer service) */
private string $customerServiceEmail = '', 
    // TCMSFieldVarchar
/** @var string - VAT registration number */
private string $shopvatnumber = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopBankAccount> - Bank accounts */
private Collection $shopBankAccountCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, dataExtranetUser> - Customers */
private Collection $dataExtranetUserCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Length of product history of an user */
private string $dataExtranetUserShopArticleHistoryMaxArticleCount = '20', 
    // TCMSFieldVarchar
/** @var string - Affiliate URL parameter */
private string $affiliateParameterName = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopAffiliate> - Affiliate programs */
private Collection $pkgShopAffiliateCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticleImageSize> - Size of product images */
private Collection $shopArticleImageSizeCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopSystemInfo> - Shop specific information / text blocks (e.g. Terms and Conditions) */
private Collection $shopSystemInfoCollection = new ArrayCollection()
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
/** @var Collection<int, shopSearchLog> - Search log */
private Collection $shopSearchLogCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopSearchFieldWeight> - Fields weight */
private Collection $shopSearchFieldWeightCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopSearchIgnoreWord> - Words to be ignored in searches */
private Collection $shopSearchIgnoreWordCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopSearchKeywordArticle> - Manually selected search results */
private Collection $shopSearchKeywordArticleCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopSearchCache> - Search cache */
private Collection $shopSearchCacheCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Name of the spot in the layouts containing the basket module */
private string $basketSpotName = '', 
    // TCMSFieldVarchar
/** @var string - Name of the spot containing the central shop handler */
private string $shopCentralHandlerSpotName = 'oShopCentralHandler', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopStockMessage> - Stock messages */
private Collection $shopStockMessageCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Export key */
private string $exportKey = '', 
    // TCMSFieldVarchar
/** @var string - Maximum size of cookie for item history (in KB) */
private string $dataExtranetUserShopArticleHistoryMaxCookieSize = '0', 
    // TCMSFieldVarchar
/** @var string - Shipping delay (days) */
private string $shopreviewmailMailDelay = '4', 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopFooterCategory> - Footer categories */
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
* @return Collection<int, shopOrderStatusCode>
*/
public function getShopOrderStatusCodeCollection(): Collection
{
    return $this->shopOrderStatusCodeCollection;
}

public function addShopOrderStatusCodeCollection(shopOrderStatusCode $shopOrderStatusCode): self
{
    if (!$this->shopOrderStatusCodeCollection->contains($shopOrderStatusCode)) {
        $this->shopOrderStatusCodeCollection->add($shopOrderStatusCode);
        $shopOrderStatusCode->setShop($this);
    }

    return $this;
}

public function removeShopOrderStatusCodeCollection(shopOrderStatusCode $shopOrderStatusCode): self
{
    if ($this->shopOrderStatusCodeCollection->removeElement($shopOrderStatusCode)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderStatusCode->getShop() === $this) {
            $shopOrderStatusCode->setShop(null);
        }
    }

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
* @return Collection<int, shopBankAccount>
*/
public function getShopBankAccountCollection(): Collection
{
    return $this->shopBankAccountCollection;
}

public function addShopBankAccountCollection(shopBankAccount $shopBankAccount): self
{
    if (!$this->shopBankAccountCollection->contains($shopBankAccount)) {
        $this->shopBankAccountCollection->add($shopBankAccount);
        $shopBankAccount->setShop($this);
    }

    return $this;
}

public function removeShopBankAccountCollection(shopBankAccount $shopBankAccount): self
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
* @return Collection<int, dataExtranetUser>
*/
public function getDataExtranetUserCollection(): Collection
{
    return $this->dataExtranetUserCollection;
}

public function addDataExtranetUserCollection(dataExtranetUser $dataExtranetUser): self
{
    if (!$this->dataExtranetUserCollection->contains($dataExtranetUser)) {
        $this->dataExtranetUserCollection->add($dataExtranetUser);
        $dataExtranetUser->setShop($this);
    }

    return $this;
}

public function removeDataExtranetUserCollection(dataExtranetUser $dataExtranetUser): self
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
* @return Collection<int, pkgShopAffiliate>
*/
public function getPkgShopAffiliateCollection(): Collection
{
    return $this->pkgShopAffiliateCollection;
}

public function addPkgShopAffiliateCollection(pkgShopAffiliate $pkgShopAffiliate): self
{
    if (!$this->pkgShopAffiliateCollection->contains($pkgShopAffiliate)) {
        $this->pkgShopAffiliateCollection->add($pkgShopAffiliate);
        $pkgShopAffiliate->setShop($this);
    }

    return $this;
}

public function removePkgShopAffiliateCollection(pkgShopAffiliate $pkgShopAffiliate): self
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
* @return Collection<int, shopArticleImageSize>
*/
public function getShopArticleImageSizeCollection(): Collection
{
    return $this->shopArticleImageSizeCollection;
}

public function addShopArticleImageSizeCollection(shopArticleImageSize $shopArticleImageSize): self
{
    if (!$this->shopArticleImageSizeCollection->contains($shopArticleImageSize)) {
        $this->shopArticleImageSizeCollection->add($shopArticleImageSize);
        $shopArticleImageSize->setShop($this);
    }

    return $this;
}

public function removeShopArticleImageSizeCollection(shopArticleImageSize $shopArticleImageSize): self
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
* @return Collection<int, shopSystemInfo>
*/
public function getShopSystemInfoCollection(): Collection
{
    return $this->shopSystemInfoCollection;
}

public function addShopSystemInfoCollection(shopSystemInfo $shopSystemInfo): self
{
    if (!$this->shopSystemInfoCollection->contains($shopSystemInfo)) {
        $this->shopSystemInfoCollection->add($shopSystemInfo);
        $shopSystemInfo->setShop($this);
    }

    return $this;
}

public function removeShopSystemInfoCollection(shopSystemInfo $shopSystemInfo): self
{
    if ($this->shopSystemInfoCollection->removeElement($shopSystemInfo)) {
        // set the owning side to null (unless already changed)
        if ($shopSystemInfo->getShop() === $this) {
            $shopSystemInfo->setShop(null);
        }
    }

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
* @return Collection<int, shopSearchLog>
*/
public function getShopSearchLogCollection(): Collection
{
    return $this->shopSearchLogCollection;
}

public function addShopSearchLogCollection(shopSearchLog $shopSearchLog): self
{
    if (!$this->shopSearchLogCollection->contains($shopSearchLog)) {
        $this->shopSearchLogCollection->add($shopSearchLog);
        $shopSearchLog->setShop($this);
    }

    return $this;
}

public function removeShopSearchLogCollection(shopSearchLog $shopSearchLog): self
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
* @return Collection<int, shopSearchFieldWeight>
*/
public function getShopSearchFieldWeightCollection(): Collection
{
    return $this->shopSearchFieldWeightCollection;
}

public function addShopSearchFieldWeightCollection(shopSearchFieldWeight $shopSearchFieldWeight): self
{
    if (!$this->shopSearchFieldWeightCollection->contains($shopSearchFieldWeight)) {
        $this->shopSearchFieldWeightCollection->add($shopSearchFieldWeight);
        $shopSearchFieldWeight->setShop($this);
    }

    return $this;
}

public function removeShopSearchFieldWeightCollection(shopSearchFieldWeight $shopSearchFieldWeight): self
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
* @return Collection<int, shopSearchIgnoreWord>
*/
public function getShopSearchIgnoreWordCollection(): Collection
{
    return $this->shopSearchIgnoreWordCollection;
}

public function addShopSearchIgnoreWordCollection(shopSearchIgnoreWord $shopSearchIgnoreWord): self
{
    if (!$this->shopSearchIgnoreWordCollection->contains($shopSearchIgnoreWord)) {
        $this->shopSearchIgnoreWordCollection->add($shopSearchIgnoreWord);
        $shopSearchIgnoreWord->setShop($this);
    }

    return $this;
}

public function removeShopSearchIgnoreWordCollection(shopSearchIgnoreWord $shopSearchIgnoreWord): self
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
* @return Collection<int, shopSearchKeywordArticle>
*/
public function getShopSearchKeywordArticleCollection(): Collection
{
    return $this->shopSearchKeywordArticleCollection;
}

public function addShopSearchKeywordArticleCollection(shopSearchKeywordArticle $shopSearchKeywordArticle): self
{
    if (!$this->shopSearchKeywordArticleCollection->contains($shopSearchKeywordArticle)) {
        $this->shopSearchKeywordArticleCollection->add($shopSearchKeywordArticle);
        $shopSearchKeywordArticle->setShop($this);
    }

    return $this;
}

public function removeShopSearchKeywordArticleCollection(shopSearchKeywordArticle $shopSearchKeywordArticle): self
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
* @return Collection<int, shopSearchCache>
*/
public function getShopSearchCacheCollection(): Collection
{
    return $this->shopSearchCacheCollection;
}

public function addShopSearchCacheCollection(shopSearchCache $shopSearchCache): self
{
    if (!$this->shopSearchCacheCollection->contains($shopSearchCache)) {
        $this->shopSearchCacheCollection->add($shopSearchCache);
        $shopSearchCache->setShop($this);
    }

    return $this;
}

public function removeShopSearchCacheCollection(shopSearchCache $shopSearchCache): self
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
* @return Collection<int, shopStockMessage>
*/
public function getShopStockMessageCollection(): Collection
{
    return $this->shopStockMessageCollection;
}

public function addShopStockMessageCollection(shopStockMessage $shopStockMessage): self
{
    if (!$this->shopStockMessageCollection->contains($shopStockMessage)) {
        $this->shopStockMessageCollection->add($shopStockMessage);
        $shopStockMessage->setShop($this);
    }

    return $this;
}

public function removeShopStockMessageCollection(shopStockMessage $shopStockMessage): self
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
* @return Collection<int, pkgShopFooterCategory>
*/
public function getPkgShopFooterCategoryCollection(): Collection
{
    return $this->pkgShopFooterCategoryCollection;
}

public function addPkgShopFooterCategoryCollection(pkgShopFooterCategory $pkgShopFooterCategory): self
{
    if (!$this->pkgShopFooterCategoryCollection->contains($pkgShopFooterCategory)) {
        $this->pkgShopFooterCategoryCollection->add($pkgShopFooterCategory);
        $pkgShopFooterCategory->setShop($this);
    }

    return $this;
}

public function removePkgShopFooterCategoryCollection(pkgShopFooterCategory $pkgShopFooterCategory): self
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
