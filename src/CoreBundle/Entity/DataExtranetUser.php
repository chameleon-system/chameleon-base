<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetUser {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\Shop|null - Belongs to shop */
private \ChameleonSystem\CoreBundle\Entity\Shop|null $shop = null,
/** @var null|string - Belongs to shop */
private ?string $shopId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null - Name */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $dataExtranetSalutation = null,
/** @var null|string - Name */
private ?string $dataExtranetSalutationId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry|null - Country */
private \ChameleonSystem\CoreBundle\Entity\DataCountry|null $dataCountry = null,
/** @var null|string - Country */
private ?string $dataCountryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null - Last billing address */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null $defaultBillingAddress = null,
/** @var null|string - Last billing address */
private ?string $defaultBillingAddressId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null - Last used shipping address */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null $defaultShippingAddress = null,
/** @var null|string - Last used shipping address */
private ?string $defaultShippingAddressId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null - Currency */
private \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null $pkgShopCurrency = null,
/** @var null|string - Currency */
private ?string $pkgShopCurrencyId = null
, 
    // TCMSFieldNumber
/** @var int - Customer number */
private int $customerNumber = 0, 
    // TCMSFieldVarchar
/** @var string - Login */
private string $name = '', 
    // TCMSFieldPasswordEncrypted
/** @var string - Password */
private string $password = '', 
    // TCMSFieldPasswordEncrypted
/** @var string - Password change key */
private string $passwordChangeKey = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Date of the request to change password */
private \DateTime|null $passwordChangeTimeStamp = null, 
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
    // TCMSFieldEmail
/** @var string - Email */
private string $email = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Customer groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Fax */
private string $fax = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress[] - Addresses */
private \Doctrine\Common\Collections\Collection $dataExtranetUserAddressCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Session key */
private string $sessionKey = '', 
    // TCMSFieldNumber
/** @var int - Login timestamp */
private int $loginTimestamp = 0, 
    // TCMSFieldNumber
/** @var int - Login salt */
private int $loginSalt = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserLoginHistory[] - Login process */
private \Doctrine\Common\Collections\Collection $dataExtranetUserLoginHistoryCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldDateTime
/** @var \DateTime|null - Date of subscription */
private \DateTime|null $datecreated = null, 
    // TCMSFieldBoolean
/** @var bool - Confirmed */
private bool $confirmed = false, 
    // TCMSFieldVarchar
/** @var string - Confirmation key */
private string $tmpconfirmkey = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Confirmed on */
private \DateTime|null $confirmedon = null, 
    // TCMSFieldBoolean
/** @var bool - Registration email sent */
private bool $regEmailSend = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopUserPurchasedVoucher[] - Bought vouchers */
private \Doctrine\Common\Collections\Collection $shopUserPurchasedVoucherCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopUserNoticeList[] - Notice list */
private \Doctrine\Common\Collections\Collection $shopUserNoticeListCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder[] - Orders */
private \Doctrine\Common\Collections\Collection $shopOrderCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserShopArticleHistory[] - Last viewed */
private \Doctrine\Common\Collections\Collection $dataExtranetUserShopArticleHistoryCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchLog[] - Searches executed by customer */
private \Doctrine\Common\Collections\Collection $shopSearchLogCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSuggestArticleLog[] - Customer recommendations */
private \Doctrine\Common\Collections\Collection $shopSuggestArticleLogCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleReview[] - Reviews */
private \Doctrine\Common\Collections\Collection $shopArticleReviewCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist[] - Wish list */
private \Doctrine\Common\Collections\Collection $pkgShopWishlistCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldDate
/** @var \DateTime|null - Date of birth */
private \DateTime|null $birthdate = null  ) {}

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
    // TCMSFieldLookup
public function getShop(): \ChameleonSystem\CoreBundle\Entity\Shop|null
{
    return $this->shop;
}
public function setShop(\ChameleonSystem\CoreBundle\Entity\Shop|null $shop): self
{
    $this->shop = $shop;
    $this->shopId = $shop?->getId();

    return $this;
}
public function getShopId(): ?string
{
    return $this->shopId;
}
public function setShopId(?string $shopId): self
{
    $this->shopId = $shopId;
    // todo - load new id
    //$this->shopId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldNumber
public function getCustomerNumber(): int
{
    return $this->customerNumber;
}
public function setCustomerNumber(int $customerNumber): self
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


  
    // TCMSFieldPasswordEncrypted
public function getPassword(): string
{
    return $this->password;
}
public function setPassword(string $password): self
{
    $this->password = $password;

    return $this;
}


  
    // TCMSFieldPasswordEncrypted
public function getPasswordChangeKey(): string
{
    return $this->passwordChangeKey;
}
public function setPasswordChangeKey(string $passwordChangeKey): self
{
    $this->passwordChangeKey = $passwordChangeKey;

    return $this;
}


  
    // TCMSFieldDateTime
public function getPasswordChangeTimeStamp(): \DateTime|null
{
    return $this->passwordChangeTimeStamp;
}
public function setPasswordChangeTimeStamp(\DateTime|null $passwordChangeTimeStamp): self
{
    $this->passwordChangeTimeStamp = $passwordChangeTimeStamp;

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


  
    // TCMSFieldEmail
public function getEmail(): string
{
    return $this->email;
}
public function setEmail(string $email): self
{
    $this->email = $email;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getDataExtranetGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetGroupMlt;
}
public function setDataExtranetGroupMlt(\Doctrine\Common\Collections\Collection $dataExtranetGroupMlt): self
{
    $this->dataExtranetGroupMlt = $dataExtranetGroupMlt;

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
public function getDataExtranetUserAddressCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetUserAddressCollection;
}
public function setDataExtranetUserAddressCollection(\Doctrine\Common\Collections\Collection $dataExtranetUserAddressCollection): self
{
    $this->dataExtranetUserAddressCollection = $dataExtranetUserAddressCollection;

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


  
    // TCMSFieldNumber
public function getLoginTimestamp(): int
{
    return $this->loginTimestamp;
}
public function setLoginTimestamp(int $loginTimestamp): self
{
    $this->loginTimestamp = $loginTimestamp;

    return $this;
}


  
    // TCMSFieldNumber
public function getLoginSalt(): int
{
    return $this->loginSalt;
}
public function setLoginSalt(int $loginSalt): self
{
    $this->loginSalt = $loginSalt;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getDataExtranetUserLoginHistoryCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetUserLoginHistoryCollection;
}
public function setDataExtranetUserLoginHistoryCollection(\Doctrine\Common\Collections\Collection $dataExtranetUserLoginHistoryCollection): self
{
    $this->dataExtranetUserLoginHistoryCollection = $dataExtranetUserLoginHistoryCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getDefaultBillingAddress(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null
{
    return $this->defaultBillingAddress;
}
public function setDefaultBillingAddress(\ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null $defaultBillingAddress): self
{
    $this->defaultBillingAddress = $defaultBillingAddress;
    $this->defaultBillingAddressId = $defaultBillingAddress?->getId();

    return $this;
}
public function getDefaultBillingAddressId(): ?string
{
    return $this->defaultBillingAddressId;
}
public function setDefaultBillingAddressId(?string $defaultBillingAddressId): self
{
    $this->defaultBillingAddressId = $defaultBillingAddressId;
    // todo - load new id
    //$this->defaultBillingAddressId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getDefaultShippingAddress(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null
{
    return $this->defaultShippingAddress;
}
public function setDefaultShippingAddress(\ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress|null $defaultShippingAddress): self
{
    $this->defaultShippingAddress = $defaultShippingAddress;
    $this->defaultShippingAddressId = $defaultShippingAddress?->getId();

    return $this;
}
public function getDefaultShippingAddressId(): ?string
{
    return $this->defaultShippingAddressId;
}
public function setDefaultShippingAddressId(?string $defaultShippingAddressId): self
{
    $this->defaultShippingAddressId = $defaultShippingAddressId;
    // todo - load new id
    //$this->defaultShippingAddressId = $?->getId();

    return $this;
}



  
    // TCMSFieldDateTime
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
public function isConfirmed(): bool
{
    return $this->confirmed;
}
public function setConfirmed(bool $confirmed): self
{
    $this->confirmed = $confirmed;

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


  
    // TCMSFieldDateTime
public function getConfirmedon(): \DateTime|null
{
    return $this->confirmedon;
}
public function setConfirmedon(\DateTime|null $confirmedon): self
{
    $this->confirmedon = $confirmedon;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRegEmailSend(): bool
{
    return $this->regEmailSend;
}
public function setRegEmailSend(bool $regEmailSend): self
{
    $this->regEmailSend = $regEmailSend;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopUserPurchasedVoucherCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopUserPurchasedVoucherCollection;
}
public function setShopUserPurchasedVoucherCollection(\Doctrine\Common\Collections\Collection $shopUserPurchasedVoucherCollection): self
{
    $this->shopUserPurchasedVoucherCollection = $shopUserPurchasedVoucherCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopUserNoticeListCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopUserNoticeListCollection;
}
public function setShopUserNoticeListCollection(\Doctrine\Common\Collections\Collection $shopUserNoticeListCollection): self
{
    $this->shopUserNoticeListCollection = $shopUserNoticeListCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopOrderCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderCollection;
}
public function setShopOrderCollection(\Doctrine\Common\Collections\Collection $shopOrderCollection): self
{
    $this->shopOrderCollection = $shopOrderCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getDataExtranetUserShopArticleHistoryCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetUserShopArticleHistoryCollection;
}
public function setDataExtranetUserShopArticleHistoryCollection(\Doctrine\Common\Collections\Collection $dataExtranetUserShopArticleHistoryCollection): self
{
    $this->dataExtranetUserShopArticleHistoryCollection = $dataExtranetUserShopArticleHistoryCollection;

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
public function getShopSuggestArticleLogCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSuggestArticleLogCollection;
}
public function setShopSuggestArticleLogCollection(\Doctrine\Common\Collections\Collection $shopSuggestArticleLogCollection): self
{
    $this->shopSuggestArticleLogCollection = $shopSuggestArticleLogCollection;

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


  
    // TCMSFieldPropertyTable
public function getPkgShopWishlistCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopWishlistCollection;
}
public function setPkgShopWishlistCollection(\Doctrine\Common\Collections\Collection $pkgShopWishlistCollection): self
{
    $this->pkgShopWishlistCollection = $pkgShopWishlistCollection;

    return $this;
}


  
    // TCMSFieldDate
public function getBirthdate(): \DateTime|null
{
    return $this->birthdate;
}
public function setBirthdate(\DateTime|null $birthdate): self
{
    $this->birthdate = $birthdate;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopCurrency(): \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null
{
    return $this->pkgShopCurrency;
}
public function setPkgShopCurrency(\ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null $pkgShopCurrency): self
{
    $this->pkgShopCurrency = $pkgShopCurrency;
    $this->pkgShopCurrencyId = $pkgShopCurrency?->getId();

    return $this;
}
public function getPkgShopCurrencyId(): ?string
{
    return $this->pkgShopCurrencyId;
}
public function setPkgShopCurrencyId(?string $pkgShopCurrencyId): self
{
    $this->pkgShopCurrencyId = $pkgShopCurrencyId;
    // todo - load new id
    //$this->pkgShopCurrencyId = $?->getId();

    return $this;
}



  
}
