<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;
use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation;
use ChameleonSystem\CoreBundle\Entity\DataCountry;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUserLoginHistory;
use ChameleonSystem\CoreBundle\Entity\ShopUserPurchasedVoucher;
use ChameleonSystem\CoreBundle\Entity\ShopUserNoticeList;
use ChameleonSystem\CoreBundle\Entity\ShopOrder;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUserShopArticleHistory;
use ChameleonSystem\CoreBundle\Entity\ShopSearchLog;
use ChameleonSystem\CoreBundle\Entity\ShopSuggestArticleLog;
use ChameleonSystem\CoreBundle\Entity\ShopArticleReview;
use ChameleonSystem\CoreBundle\Entity\PkgShopWishlist;
use ChameleonSystem\CoreBundle\Entity\PkgShopCurrency;

class DataExtranetUser {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldLookup
/** @var CmsPortal|null - Belongs to portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Customer number */
private string $customerNumber = '', 
    // TCMSFieldVarchar
/** @var string - Login */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Password */
private string $password = '', 
    // TCMSFieldVarchar
/** @var string - Password change key */
private string $passwordChangeKey = '', 
    // TCMSFieldLookup
/** @var DataExtranetSalutation|null - Name */
private ?DataExtranetSalutation $dataExtranetSalutation = null
, 
    // TCMSFieldVarchar
/** @var string - First name */
private string $firstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $lastname = '', 
    // TCMSFieldVarchar
/** @var string - Company */
private string $company = '', 
    // TCMSFieldVarchar
/** @var string - Street */
private string $street = '', 
    // TCMSFieldVarchar
/** @var string - Street Number */
private string $streetnr = '', 
    // TCMSFieldVarchar
/** @var string - Zip code */
private string $postalcode = '', 
    // TCMSFieldVarchar
/** @var string - City */
private string $city = '', 
    // TCMSFieldLookup
/** @var DataCountry|null - Country */
private ?DataCountry $dataCountry = null
, 
    // TCMSFieldVarchar
/** @var string - USTID */
private string $vatId = '', 
    // TCMSFieldVarchar
/** @var string - Telephone */
private string $telefon = '', 
    // TCMSFieldVarchar
/** @var string - Mobile */
private string $mobile = '', 
    // TCMSFieldVarchar
/** @var string - Address appendix */
private string $addressAdditionalInfo = '', 
    // TCMSFieldVarchar
/** @var string - Alias */
private string $aliasName = '', 
    // TCMSFieldVarchar
/** @var string - Email */
private string $email = '', 
    // TCMSFieldVarchar
/** @var string - Fax */
private string $fax = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, DataExtranetUserAddress> - Addresses */
private Collection $dataExtranetUserAddressCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Session key */
private string $sessionKey = '', 
    // TCMSFieldVarchar
/** @var string - Login timestamp */
private string $loginTimestamp = '', 
    // TCMSFieldVarchar
/** @var string - Login salt */
private string $loginSalt = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, DataExtranetUserLoginHistory> - Login process */
private Collection $dataExtranetUserLoginHistoryCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var DataExtranetUserAddress|null - Last billing address */
private ?DataExtranetUserAddress $defaultBillingAddress = null
, 
    // TCMSFieldLookup
/** @var DataExtranetUserAddress|null - Last used shipping address */
private ?DataExtranetUserAddress $defaultShippingAddress = null
, 
    // TCMSFieldVarchar
/** @var string - Confirmation key */
private string $tmpconfirmkey = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopUserPurchasedVoucher> - Bought vouchers */
private Collection $shopUserPurchasedVoucherCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopUserNoticeList> - Notice list */
private Collection $shopUserNoticeListCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopOrder> - Orders */
private Collection $shopOrderCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, DataExtranetUserShopArticleHistory> - Last viewed */
private Collection $dataExtranetUserShopArticleHistoryCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSearchLog> - Searches executed by customer */
private Collection $shopSearchLogCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSuggestArticleLog> - Customer recommendations */
private Collection $shopSuggestArticleLogCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticleReview> - Reviews */
private Collection $shopArticleReviewCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgShopWishlist> - Wish list */
private Collection $pkgShopWishlistCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var PkgShopCurrency|null - Currency */
private ?PkgShopCurrency $pkgShopCurrency = null
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
    // TCMSFieldLookup
public function getShop(): ?Shop
{
    return $this->shop;
}

public function setShop(?Shop $shop): self
{
    $this->shop = $shop;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsPortal(): ?CmsPortal
{
    return $this->cmsPortal;
}

public function setCmsPortal(?CmsPortal $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCustomerNumber(): string
{
    return $this->customerNumber;
}
public function setCustomerNumber(string $customerNumber): self
{
    $this->customerNumber = $customerNumber;

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
public function getPassword(): string
{
    return $this->password;
}
public function setPassword(string $password): self
{
    $this->password = $password;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPasswordChangeKey(): string
{
    return $this->passwordChangeKey;
}
public function setPasswordChangeKey(string $passwordChangeKey): self
{
    $this->passwordChangeKey = $passwordChangeKey;

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


  
    // TCMSFieldVarchar
public function getFirstname(): string
{
    return $this->firstname;
}
public function setFirstname(string $firstname): self
{
    $this->firstname = $firstname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLastname(): string
{
    return $this->lastname;
}
public function setLastname(string $lastname): self
{
    $this->lastname = $lastname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCompany(): string
{
    return $this->company;
}
public function setCompany(string $company): self
{
    $this->company = $company;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStreet(): string
{
    return $this->street;
}
public function setStreet(string $street): self
{
    $this->street = $street;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStreetnr(): string
{
    return $this->streetnr;
}
public function setStreetnr(string $streetnr): self
{
    $this->streetnr = $streetnr;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPostalcode(): string
{
    return $this->postalcode;
}
public function setPostalcode(string $postalcode): self
{
    $this->postalcode = $postalcode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCity(): string
{
    return $this->city;
}
public function setCity(string $city): self
{
    $this->city = $city;

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
public function getVatId(): string
{
    return $this->vatId;
}
public function setVatId(string $vatId): self
{
    $this->vatId = $vatId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTelefon(): string
{
    return $this->telefon;
}
public function setTelefon(string $telefon): self
{
    $this->telefon = $telefon;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMobile(): string
{
    return $this->mobile;
}
public function setMobile(string $mobile): self
{
    $this->mobile = $mobile;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAddressAdditionalInfo(): string
{
    return $this->addressAdditionalInfo;
}
public function setAddressAdditionalInfo(string $addressAdditionalInfo): self
{
    $this->addressAdditionalInfo = $addressAdditionalInfo;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAliasName(): string
{
    return $this->aliasName;
}
public function setAliasName(string $aliasName): self
{
    $this->aliasName = $aliasName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getEmail(): string
{
    return $this->email;
}
public function setEmail(string $email): self
{
    $this->email = $email;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFax(): string
{
    return $this->fax;
}
public function setFax(string $fax): self
{
    $this->fax = $fax;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, DataExtranetUserAddress>
*/
public function getDataExtranetUserAddressCollection(): Collection
{
    return $this->dataExtranetUserAddressCollection;
}

public function addDataExtranetUserAddressCollection(DataExtranetUserAddress $dataExtranetUserAddress): self
{
    if (!$this->dataExtranetUserAddressCollection->contains($dataExtranetUserAddress)) {
        $this->dataExtranetUserAddressCollection->add($dataExtranetUserAddress);
        $dataExtranetUserAddress->setDataExtranetUser($this);
    }

    return $this;
}

public function removeDataExtranetUserAddressCollection(DataExtranetUserAddress $dataExtranetUserAddress): self
{
    if ($this->dataExtranetUserAddressCollection->removeElement($dataExtranetUserAddress)) {
        // set the owning side to null (unless already changed)
        if ($dataExtranetUserAddress->getDataExtranetUser() === $this) {
            $dataExtranetUserAddress->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getSessionKey(): string
{
    return $this->sessionKey;
}
public function setSessionKey(string $sessionKey): self
{
    $this->sessionKey = $sessionKey;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLoginTimestamp(): string
{
    return $this->loginTimestamp;
}
public function setLoginTimestamp(string $loginTimestamp): self
{
    $this->loginTimestamp = $loginTimestamp;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLoginSalt(): string
{
    return $this->loginSalt;
}
public function setLoginSalt(string $loginSalt): self
{
    $this->loginSalt = $loginSalt;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, DataExtranetUserLoginHistory>
*/
public function getDataExtranetUserLoginHistoryCollection(): Collection
{
    return $this->dataExtranetUserLoginHistoryCollection;
}

public function addDataExtranetUserLoginHistoryCollection(DataExtranetUserLoginHistory $dataExtranetUserLoginHistory): self
{
    if (!$this->dataExtranetUserLoginHistoryCollection->contains($dataExtranetUserLoginHistory)) {
        $this->dataExtranetUserLoginHistoryCollection->add($dataExtranetUserLoginHistory);
        $dataExtranetUserLoginHistory->setDataExtranetUser($this);
    }

    return $this;
}

public function removeDataExtranetUserLoginHistoryCollection(DataExtranetUserLoginHistory $dataExtranetUserLoginHistory): self
{
    if ($this->dataExtranetUserLoginHistoryCollection->removeElement($dataExtranetUserLoginHistory)) {
        // set the owning side to null (unless already changed)
        if ($dataExtranetUserLoginHistory->getDataExtranetUser() === $this) {
            $dataExtranetUserLoginHistory->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
public function getDefaultBillingAddress(): ?DataExtranetUserAddress
{
    return $this->defaultBillingAddress;
}

public function setDefaultBillingAddress(?DataExtranetUserAddress $defaultBillingAddress): self
{
    $this->defaultBillingAddress = $defaultBillingAddress;

    return $this;
}


  
    // TCMSFieldLookup
public function getDefaultShippingAddress(): ?DataExtranetUserAddress
{
    return $this->defaultShippingAddress;
}

public function setDefaultShippingAddress(?DataExtranetUserAddress $defaultShippingAddress): self
{
    $this->defaultShippingAddress = $defaultShippingAddress;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTmpconfirmkey(): string
{
    return $this->tmpconfirmkey;
}
public function setTmpconfirmkey(string $tmpconfirmkey): self
{
    $this->tmpconfirmkey = $tmpconfirmkey;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopUserPurchasedVoucher>
*/
public function getShopUserPurchasedVoucherCollection(): Collection
{
    return $this->shopUserPurchasedVoucherCollection;
}

public function addShopUserPurchasedVoucherCollection(ShopUserPurchasedVoucher $shopUserPurchasedVoucher): self
{
    if (!$this->shopUserPurchasedVoucherCollection->contains($shopUserPurchasedVoucher)) {
        $this->shopUserPurchasedVoucherCollection->add($shopUserPurchasedVoucher);
        $shopUserPurchasedVoucher->setDataExtranetUser($this);
    }

    return $this;
}

public function removeShopUserPurchasedVoucherCollection(ShopUserPurchasedVoucher $shopUserPurchasedVoucher): self
{
    if ($this->shopUserPurchasedVoucherCollection->removeElement($shopUserPurchasedVoucher)) {
        // set the owning side to null (unless already changed)
        if ($shopUserPurchasedVoucher->getDataExtranetUser() === $this) {
            $shopUserPurchasedVoucher->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopUserNoticeList>
*/
public function getShopUserNoticeListCollection(): Collection
{
    return $this->shopUserNoticeListCollection;
}

public function addShopUserNoticeListCollection(ShopUserNoticeList $shopUserNoticeList): self
{
    if (!$this->shopUserNoticeListCollection->contains($shopUserNoticeList)) {
        $this->shopUserNoticeListCollection->add($shopUserNoticeList);
        $shopUserNoticeList->setDataExtranetUser($this);
    }

    return $this;
}

public function removeShopUserNoticeListCollection(ShopUserNoticeList $shopUserNoticeList): self
{
    if ($this->shopUserNoticeListCollection->removeElement($shopUserNoticeList)) {
        // set the owning side to null (unless already changed)
        if ($shopUserNoticeList->getDataExtranetUser() === $this) {
            $shopUserNoticeList->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopOrder>
*/
public function getShopOrderCollection(): Collection
{
    return $this->shopOrderCollection;
}

public function addShopOrderCollection(ShopOrder $shopOrder): self
{
    if (!$this->shopOrderCollection->contains($shopOrder)) {
        $this->shopOrderCollection->add($shopOrder);
        $shopOrder->setDataExtranetUser($this);
    }

    return $this;
}

public function removeShopOrderCollection(ShopOrder $shopOrder): self
{
    if ($this->shopOrderCollection->removeElement($shopOrder)) {
        // set the owning side to null (unless already changed)
        if ($shopOrder->getDataExtranetUser() === $this) {
            $shopOrder->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, DataExtranetUserShopArticleHistory>
*/
public function getDataExtranetUserShopArticleHistoryCollection(): Collection
{
    return $this->dataExtranetUserShopArticleHistoryCollection;
}

public function addDataExtranetUserShopArticleHistoryCollection(DataExtranetUserShopArticleHistory $dataExtranetUserShopArticleHistory): self
{
    if (!$this->dataExtranetUserShopArticleHistoryCollection->contains($dataExtranetUserShopArticleHistory)) {
        $this->dataExtranetUserShopArticleHistoryCollection->add($dataExtranetUserShopArticleHistory);
        $dataExtranetUserShopArticleHistory->setDataExtranetUser($this);
    }

    return $this;
}

public function removeDataExtranetUserShopArticleHistoryCollection(DataExtranetUserShopArticleHistory $dataExtranetUserShopArticleHistory): self
{
    if ($this->dataExtranetUserShopArticleHistoryCollection->removeElement($dataExtranetUserShopArticleHistory)) {
        // set the owning side to null (unless already changed)
        if ($dataExtranetUserShopArticleHistory->getDataExtranetUser() === $this) {
            $dataExtranetUserShopArticleHistory->setDataExtranetUser(null);
        }
    }

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
        $shopSearchLog->setDataExtranetUser($this);
    }

    return $this;
}

public function removeShopSearchLogCollection(ShopSearchLog $shopSearchLog): self
{
    if ($this->shopSearchLogCollection->removeElement($shopSearchLog)) {
        // set the owning side to null (unless already changed)
        if ($shopSearchLog->getDataExtranetUser() === $this) {
            $shopSearchLog->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSuggestArticleLog>
*/
public function getShopSuggestArticleLogCollection(): Collection
{
    return $this->shopSuggestArticleLogCollection;
}

public function addShopSuggestArticleLogCollection(ShopSuggestArticleLog $shopSuggestArticleLog): self
{
    if (!$this->shopSuggestArticleLogCollection->contains($shopSuggestArticleLog)) {
        $this->shopSuggestArticleLogCollection->add($shopSuggestArticleLog);
        $shopSuggestArticleLog->setDataExtranetUser($this);
    }

    return $this;
}

public function removeShopSuggestArticleLogCollection(ShopSuggestArticleLog $shopSuggestArticleLog): self
{
    if ($this->shopSuggestArticleLogCollection->removeElement($shopSuggestArticleLog)) {
        // set the owning side to null (unless already changed)
        if ($shopSuggestArticleLog->getDataExtranetUser() === $this) {
            $shopSuggestArticleLog->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopArticleReview>
*/
public function getShopArticleReviewCollection(): Collection
{
    return $this->shopArticleReviewCollection;
}

public function addShopArticleReviewCollection(ShopArticleReview $shopArticleReview): self
{
    if (!$this->shopArticleReviewCollection->contains($shopArticleReview)) {
        $this->shopArticleReviewCollection->add($shopArticleReview);
        $shopArticleReview->setDataExtranetUser($this);
    }

    return $this;
}

public function removeShopArticleReviewCollection(ShopArticleReview $shopArticleReview): self
{
    if ($this->shopArticleReviewCollection->removeElement($shopArticleReview)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleReview->getDataExtranetUser() === $this) {
            $shopArticleReview->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, PkgShopWishlist>
*/
public function getPkgShopWishlistCollection(): Collection
{
    return $this->pkgShopWishlistCollection;
}

public function addPkgShopWishlistCollection(PkgShopWishlist $pkgShopWishlist): self
{
    if (!$this->pkgShopWishlistCollection->contains($pkgShopWishlist)) {
        $this->pkgShopWishlistCollection->add($pkgShopWishlist);
        $pkgShopWishlist->setDataExtranetUser($this);
    }

    return $this;
}

public function removePkgShopWishlistCollection(PkgShopWishlist $pkgShopWishlist): self
{
    if ($this->pkgShopWishlistCollection->removeElement($pkgShopWishlist)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopWishlist->getDataExtranetUser() === $this) {
            $pkgShopWishlist->setDataExtranetUser(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopCurrency(): ?PkgShopCurrency
{
    return $this->pkgShopCurrency;
}

public function setPkgShopCurrency(?PkgShopCurrency $pkgShopCurrency): self
{
    $this->pkgShopCurrency = $pkgShopCurrency;

    return $this;
}


  
}
