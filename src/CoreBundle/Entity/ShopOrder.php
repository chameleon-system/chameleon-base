<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrder {
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
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Placed by portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Placed by portal */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null - Used rating service */
private \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null $pkgShopRatingService = null,
/** @var null|string - Used rating service */
private ?string $pkgShopRatingServiceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Shop customer */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Shop customer */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null - Salutation */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $adrBillingSalutation = null,
/** @var null|string - Salutation */
private ?string $adrBillingSalutationId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry|null - Country */
private \ChameleonSystem\CoreBundle\Entity\DataCountry|null $adrBillingCountry = null,
/** @var null|string - Country */
private ?string $adrBillingCountryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - Language */
private ?string $cmsLanguageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null - Salutation */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $adrShippingSalutation = null,
/** @var null|string - Salutation */
private ?string $adrShippingSalutationId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry|null - Country */
private \ChameleonSystem\CoreBundle\Entity\DataCountry|null $adrShippingCountry = null,
/** @var null|string - Country */
private ?string $adrShippingCountryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup|null - Shipping cost group */
private \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup|null $shopShippingGroup = null,
/** @var null|string - Shipping cost group */
private ?string $shopShippingGroupId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod|null - Payment method */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod|null $shopPaymentMethod = null,
/** @var null|string - Payment method */
private ?string $shopPaymentMethodId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null - Currency */
private \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null $pkgShopCurrency = null,
/** @var null|string - Currency */
private ?string $pkgShopCurrencyId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null - Order created via affiliate program */
private \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null $pkgShopAffiliate = null,
/** @var null|string - Order created via affiliate program */
private ?string $pkgShopAffiliateId = null
, 
    // TCMSFieldBoolean
/** @var bool - Shop rating email - was processed */
private bool $pkgShopRatingServiceMailProcessed = false, 
    // TCMSFieldBoolean
/** @var bool - User has also subscribed to the newsletter */
private bool $newsletterSignup = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage[] -  */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\AmazonPaymentIdMapping[] - Amazon Pay */
private \Doctrine\Common\Collections\Collection $amazonPaymentIdMappingCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction[] - Transactions */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentTransactionCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Order number */
private int $ordernumber = 0, 
    // TCMSFieldBoolean
/** @var bool - Shop rating email - email was sent */
private bool $pkgShopRatingServiceMailSent = false, 
    // TCMSFieldVarcharUnique
/** @var string - Basket ID (unique ID that is already assigned in the order process) */
private string $orderIdent = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderItem[] - Items */
private \Doctrine\Common\Collections\Collection $shopOrderItemCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Customer number */
private int $customerNumber = 0, 
    // TCMSFieldEmail
/** @var string - Customer email */
private string $userEmail = '', 
    // TCMSFieldVarchar
/** @var string - Company */
private string $adrBillingCompany = '', 
    // TCMSFieldVarchar
/** @var string - First name */
private string $adrBillingFirstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $adrBillingLastname = '', 
    // TCMSFieldVarchar
/** @var string - Address appendix */
private string $adrBillingAdditionalInfo = '', 
    // TCMSFieldVarchar
/** @var string - Street */
private string $adrBillingStreet = '', 
    // TCMSFieldVarchar
/** @var string - Street number */
private string $adrBillingStreetnr = '', 
    // TCMSFieldVarchar
/** @var string - City */
private string $adrBillingCity = '', 
    // TCMSFieldVarchar
/** @var string - Zip code */
private string $adrBillingPostalcode = '', 
    // TCMSFieldVarchar
/** @var string - Telephone */
private string $adrBillingTelefon = '', 
    // TCMSFieldVarchar
/** @var string - Fax */
private string $adrBillingFax = '', 
    // TCMSFieldVarchar
/** @var string - User IP */
private string $userIp = '', 
    // TCMSFieldBoolean
/** @var bool - Ship to billing address */
private bool $adrShippingUseBilling = false, 
    // TCMSFieldBoolean
/** @var bool - Shipping address is a Packstation */
private bool $adrShippingIsDhlPackstation = false, 
    // TCMSFieldVarchar
/** @var string - Company */
private string $adrShippingCompany = '', 
    // TCMSFieldVarchar
/** @var string - First name */
private string $adrShippingFirstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $adrShippingLastname = '', 
    // TCMSFieldVarchar
/** @var string - Address appendix */
private string $adrShippingAdditionalInfo = '', 
    // TCMSFieldVarchar
/** @var string - Street */
private string $adrShippingStreet = '', 
    // TCMSFieldVarchar
/** @var string - Street number */
private string $adrShippingStreetnr = '', 
    // TCMSFieldVarchar
/** @var string - City */
private string $adrShippingCity = '', 
    // TCMSFieldVarchar
/** @var string - Zip code */
private string $adrShippingPostalcode = '', 
    // TCMSFieldVarchar
/** @var string - Telephone */
private string $adrShippingTelefon = '', 
    // TCMSFieldVarchar
/** @var string - Fax */
private string $adrShippingFax = '', 
    // TCMSFieldVarchar
/** @var string - Shipping cost group – name */
private string $shopShippingGroupName = '', 
    // TCMSFieldDecimal
/** @var float - Shipping cost group – costs */
private float $shopShippingGroupPrice = 0, 
    // TCMSFieldDecimal
/** @var float - Shipping cost group – tax rate */
private float $shopShippingGroupVatPercent = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderShippingGroupParameter[] - Shipping cost group – parameter/user data */
private \Doctrine\Common\Collections\Collection $shopOrderShippingGroupParameterCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Payment method – name */
private string $shopPaymentMethodName = '', 
    // TCMSFieldDecimal
/** @var float - Payment method – costs */
private float $shopPaymentMethodPrice = 0, 
    // TCMSFieldDecimal
/** @var float - Payment method – tax rate */
private float $shopPaymentMethodVatPercent = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderPaymentMethodParameter[] - Payment method – parameter/user data */
private \Doctrine\Common\Collections\Collection $shopOrderPaymentMethodParameterCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderVat[] - Order VAT (by tax rate) */
private \Doctrine\Common\Collections\Collection $shopOrderVatCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldDecimal
/** @var float - Items value */
private float $valueArticle = 0, 
    // TCMSFieldDecimal
/** @var float - Total value */
private float $valueTotal = 0, 
    // TCMSFieldDecimal
/** @var float - Wrapping costs */
private float $valueWrapping = 0, 
    // TCMSFieldDecimal
/** @var float - Wrapping greeting card costs */
private float $valueWrappingCard = 0, 
    // TCMSFieldDecimal
/** @var float - Total voucher value */
private float $valueVouchers = 0, 
    // TCMSFieldDecimal
/** @var float - Value of the non-sponsered vouchers (discount vouchers) */
private float $valueVouchersNotSponsored = 0, 
    // TCMSFieldDecimal
/** @var float - Total discount value */
private float $valueDiscounts = 0, 
    // TCMSFieldDecimal
/** @var float - Total VAT value */
private float $valueVatTotal = 0, 
    // TCMSFieldDecimal
/** @var float - Total number of items */
private float $countArticles = 0, 
    // TCMSFieldNumber
/** @var int - Number of different items */
private int $countUniqueArticles = 0, 
    // TCMSFieldDecimal
/** @var float - Total weight (grams) */
private float $totalweight = 0, 
    // TCMSFieldDecimal
/** @var float - Total volume (cubic meters) */
private float $totalvolume = 0, 
    // TCMSFieldBoolean
/** @var bool - Saved order completely */
private bool $systemOrderSaveCompleted = false, 
    // TCMSFieldBoolean
/** @var bool - Order confirmation sent */
private bool $systemOrderNotificationSend = false, 
    // TCMSFieldBoolean
/** @var bool - Payment method executed successfully */
private bool $systemOrderPaymentMethodExecuted = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Payment method executed on */
private \DateTime|null $systemOrderPaymentMethodExecutedDate = null, 
    // TCMSFieldBoolean
/** @var bool - Paid */
private bool $orderIsPaid = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Marked as paid on */
private \DateTime|null $orderIsPaidDate = null, 
    // TCMSFieldBoolean
/** @var bool - Order was cancelled */
private bool $canceled = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Date the order was marked as cancelled */
private \DateTime|null $canceledDate = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Was exported for ERP on */
private \DateTime|null $systemOrderExportedDate = null, 
    // TCMSFieldVarchar
/** @var string - Affiliate code */
private string $affiliateCode = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucherUse[] - Used vouchers */
private \Doctrine\Common\Collections\Collection $shopVoucherUseCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderDiscount[] - Discount */
private \Doctrine\Common\Collections\Collection $shopOrderDiscountCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatus[] - Order status */
private \Doctrine\Common\Collections\Collection $shopOrderStatusCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - Mail object */
private string $objectMail = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Rating request sent on */
private \DateTime|null $pkgShopRatingServiceRatingProcessedOn = null, 
    // TCMSFieldVarchar
/** @var string - VAT ID */
private string $vatId = '', 
    // TCMSFieldText
/** @var string - Internal comment */
private string $internalComment = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Date of shipment of all products */
private \DateTime|null $pkgShopRatingServiceOrderCompletelyShipped = null  ) {}

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
    // TCMSFieldBoolean
public function isPkgShopRatingServiceMailProcessed(): bool
{
    return $this->pkgShopRatingServiceMailProcessed;
}
public function setPkgShopRatingServiceMailProcessed(bool $pkgShopRatingServiceMailProcessed): self
{
    $this->pkgShopRatingServiceMailProcessed = $pkgShopRatingServiceMailProcessed;

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



  
    // TCMSFieldBoolean
public function isNewsletterSignup(): bool
{
    return $this->newsletterSignup;
}
public function setNewsletterSignup(bool $newsletterSignup): self
{
    $this->newsletterSignup = $newsletterSignup;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopPaymentIpnMessageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentIpnMessageCollection;
}
public function setPkgShopPaymentIpnMessageCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageCollection): self
{
    $this->pkgShopPaymentIpnMessageCollection = $pkgShopPaymentIpnMessageCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getAmazonPaymentIdMappingCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->amazonPaymentIdMappingCollection;
}
public function setAmazonPaymentIdMappingCollection(\Doctrine\Common\Collections\Collection $amazonPaymentIdMappingCollection): self
{
    $this->amazonPaymentIdMappingCollection = $amazonPaymentIdMappingCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopPaymentTransactionCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentTransactionCollection;
}
public function setPkgShopPaymentTransactionCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentTransactionCollection): self
{
    $this->pkgShopPaymentTransactionCollection = $pkgShopPaymentTransactionCollection;

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
public function getOrdernumber(): int
{
    return $this->ordernumber;
}
public function setOrdernumber(int $ordernumber): self
{
    $this->ordernumber = $ordernumber;

    return $this;
}


  
    // TCMSFieldBoolean
public function isPkgShopRatingServiceMailSent(): bool
{
    return $this->pkgShopRatingServiceMailSent;
}
public function setPkgShopRatingServiceMailSent(bool $pkgShopRatingServiceMailSent): self
{
    $this->pkgShopRatingServiceMailSent = $pkgShopRatingServiceMailSent;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopRatingService(): \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null
{
    return $this->pkgShopRatingService;
}
public function setPkgShopRatingService(\ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null $pkgShopRatingService): self
{
    $this->pkgShopRatingService = $pkgShopRatingService;
    $this->pkgShopRatingServiceId = $pkgShopRatingService?->getId();

    return $this;
}
public function getPkgShopRatingServiceId(): ?string
{
    return $this->pkgShopRatingServiceId;
}
public function setPkgShopRatingServiceId(?string $pkgShopRatingServiceId): self
{
    $this->pkgShopRatingServiceId = $pkgShopRatingServiceId;
    // todo - load new id
    //$this->pkgShopRatingServiceId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarcharUnique
public function getOrderIdent(): string
{
    return $this->orderIdent;
}
public function setOrderIdent(string $orderIdent): self
{
    $this->orderIdent = $orderIdent;

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


  
    // TCMSFieldPropertyTable
public function getShopOrderItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderItemCollection;
}
public function setShopOrderItemCollection(\Doctrine\Common\Collections\Collection $shopOrderItemCollection): self
{
    $this->shopOrderItemCollection = $shopOrderItemCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataExtranetUser(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null
{
    return $this->dataExtranetUser;
}
public function setDataExtranetUser(\ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;
    $this->dataExtranetUserId = $dataExtranetUser?->getId();

    return $this;
}
public function getDataExtranetUserId(): ?string
{
    return $this->dataExtranetUserId;
}
public function setDataExtranetUserId(?string $dataExtranetUserId): self
{
    $this->dataExtranetUserId = $dataExtranetUserId;
    // todo - load new id
    //$this->dataExtranetUserId = $?->getId();

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


  
    // TCMSFieldEmail
public function getUserEmail(): string
{
    return $this->userEmail;
}
public function setUserEmail(string $userEmail): self
{
    $this->userEmail = $userEmail;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingCompany(): string
{
    return $this->adrBillingCompany;
}
public function setAdrBillingCompany(string $adrBillingCompany): self
{
    $this->adrBillingCompany = $adrBillingCompany;

    return $this;
}


  
    // TCMSFieldLookup
public function getAdrBillingSalutation(): \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null
{
    return $this->adrBillingSalutation;
}
public function setAdrBillingSalutation(\ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $adrBillingSalutation): self
{
    $this->adrBillingSalutation = $adrBillingSalutation;
    $this->adrBillingSalutationId = $adrBillingSalutation?->getId();

    return $this;
}
public function getAdrBillingSalutationId(): ?string
{
    return $this->adrBillingSalutationId;
}
public function setAdrBillingSalutationId(?string $adrBillingSalutationId): self
{
    $this->adrBillingSalutationId = $adrBillingSalutationId;
    // todo - load new id
    //$this->adrBillingSalutationId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getAdrBillingFirstname(): string
{
    return $this->adrBillingFirstname;
}
public function setAdrBillingFirstname(string $adrBillingFirstname): self
{
    $this->adrBillingFirstname = $adrBillingFirstname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingLastname(): string
{
    return $this->adrBillingLastname;
}
public function setAdrBillingLastname(string $adrBillingLastname): self
{
    $this->adrBillingLastname = $adrBillingLastname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingAdditionalInfo(): string
{
    return $this->adrBillingAdditionalInfo;
}
public function setAdrBillingAdditionalInfo(string $adrBillingAdditionalInfo): self
{
    $this->adrBillingAdditionalInfo = $adrBillingAdditionalInfo;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingStreet(): string
{
    return $this->adrBillingStreet;
}
public function setAdrBillingStreet(string $adrBillingStreet): self
{
    $this->adrBillingStreet = $adrBillingStreet;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingStreetnr(): string
{
    return $this->adrBillingStreetnr;
}
public function setAdrBillingStreetnr(string $adrBillingStreetnr): self
{
    $this->adrBillingStreetnr = $adrBillingStreetnr;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingCity(): string
{
    return $this->adrBillingCity;
}
public function setAdrBillingCity(string $adrBillingCity): self
{
    $this->adrBillingCity = $adrBillingCity;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingPostalcode(): string
{
    return $this->adrBillingPostalcode;
}
public function setAdrBillingPostalcode(string $adrBillingPostalcode): self
{
    $this->adrBillingPostalcode = $adrBillingPostalcode;

    return $this;
}


  
    // TCMSFieldLookup
public function getAdrBillingCountry(): \ChameleonSystem\CoreBundle\Entity\DataCountry|null
{
    return $this->adrBillingCountry;
}
public function setAdrBillingCountry(\ChameleonSystem\CoreBundle\Entity\DataCountry|null $adrBillingCountry): self
{
    $this->adrBillingCountry = $adrBillingCountry;
    $this->adrBillingCountryId = $adrBillingCountry?->getId();

    return $this;
}
public function getAdrBillingCountryId(): ?string
{
    return $this->adrBillingCountryId;
}
public function setAdrBillingCountryId(?string $adrBillingCountryId): self
{
    $this->adrBillingCountryId = $adrBillingCountryId;
    // todo - load new id
    //$this->adrBillingCountryId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getAdrBillingTelefon(): string
{
    return $this->adrBillingTelefon;
}
public function setAdrBillingTelefon(string $adrBillingTelefon): self
{
    $this->adrBillingTelefon = $adrBillingTelefon;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrBillingFax(): string
{
    return $this->adrBillingFax;
}
public function setAdrBillingFax(string $adrBillingFax): self
{
    $this->adrBillingFax = $adrBillingFax;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->cmsLanguage;
}
public function setCmsLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;
    $this->cmsLanguageId = $cmsLanguage?->getId();

    return $this;
}
public function getCmsLanguageId(): ?string
{
    return $this->cmsLanguageId;
}
public function setCmsLanguageId(?string $cmsLanguageId): self
{
    $this->cmsLanguageId = $cmsLanguageId;
    // todo - load new id
    //$this->cmsLanguageId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getUserIp(): string
{
    return $this->userIp;
}
public function setUserIp(string $userIp): self
{
    $this->userIp = $userIp;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAdrShippingUseBilling(): bool
{
    return $this->adrShippingUseBilling;
}
public function setAdrShippingUseBilling(bool $adrShippingUseBilling): self
{
    $this->adrShippingUseBilling = $adrShippingUseBilling;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAdrShippingIsDhlPackstation(): bool
{
    return $this->adrShippingIsDhlPackstation;
}
public function setAdrShippingIsDhlPackstation(bool $adrShippingIsDhlPackstation): self
{
    $this->adrShippingIsDhlPackstation = $adrShippingIsDhlPackstation;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingCompany(): string
{
    return $this->adrShippingCompany;
}
public function setAdrShippingCompany(string $adrShippingCompany): self
{
    $this->adrShippingCompany = $adrShippingCompany;

    return $this;
}


  
    // TCMSFieldLookup
public function getAdrShippingSalutation(): \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null
{
    return $this->adrShippingSalutation;
}
public function setAdrShippingSalutation(\ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $adrShippingSalutation): self
{
    $this->adrShippingSalutation = $adrShippingSalutation;
    $this->adrShippingSalutationId = $adrShippingSalutation?->getId();

    return $this;
}
public function getAdrShippingSalutationId(): ?string
{
    return $this->adrShippingSalutationId;
}
public function setAdrShippingSalutationId(?string $adrShippingSalutationId): self
{
    $this->adrShippingSalutationId = $adrShippingSalutationId;
    // todo - load new id
    //$this->adrShippingSalutationId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getAdrShippingFirstname(): string
{
    return $this->adrShippingFirstname;
}
public function setAdrShippingFirstname(string $adrShippingFirstname): self
{
    $this->adrShippingFirstname = $adrShippingFirstname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingLastname(): string
{
    return $this->adrShippingLastname;
}
public function setAdrShippingLastname(string $adrShippingLastname): self
{
    $this->adrShippingLastname = $adrShippingLastname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingAdditionalInfo(): string
{
    return $this->adrShippingAdditionalInfo;
}
public function setAdrShippingAdditionalInfo(string $adrShippingAdditionalInfo): self
{
    $this->adrShippingAdditionalInfo = $adrShippingAdditionalInfo;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingStreet(): string
{
    return $this->adrShippingStreet;
}
public function setAdrShippingStreet(string $adrShippingStreet): self
{
    $this->adrShippingStreet = $adrShippingStreet;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingStreetnr(): string
{
    return $this->adrShippingStreetnr;
}
public function setAdrShippingStreetnr(string $adrShippingStreetnr): self
{
    $this->adrShippingStreetnr = $adrShippingStreetnr;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingCity(): string
{
    return $this->adrShippingCity;
}
public function setAdrShippingCity(string $adrShippingCity): self
{
    $this->adrShippingCity = $adrShippingCity;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingPostalcode(): string
{
    return $this->adrShippingPostalcode;
}
public function setAdrShippingPostalcode(string $adrShippingPostalcode): self
{
    $this->adrShippingPostalcode = $adrShippingPostalcode;

    return $this;
}


  
    // TCMSFieldLookup
public function getAdrShippingCountry(): \ChameleonSystem\CoreBundle\Entity\DataCountry|null
{
    return $this->adrShippingCountry;
}
public function setAdrShippingCountry(\ChameleonSystem\CoreBundle\Entity\DataCountry|null $adrShippingCountry): self
{
    $this->adrShippingCountry = $adrShippingCountry;
    $this->adrShippingCountryId = $adrShippingCountry?->getId();

    return $this;
}
public function getAdrShippingCountryId(): ?string
{
    return $this->adrShippingCountryId;
}
public function setAdrShippingCountryId(?string $adrShippingCountryId): self
{
    $this->adrShippingCountryId = $adrShippingCountryId;
    // todo - load new id
    //$this->adrShippingCountryId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getAdrShippingTelefon(): string
{
    return $this->adrShippingTelefon;
}
public function setAdrShippingTelefon(string $adrShippingTelefon): self
{
    $this->adrShippingTelefon = $adrShippingTelefon;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAdrShippingFax(): string
{
    return $this->adrShippingFax;
}
public function setAdrShippingFax(string $adrShippingFax): self
{
    $this->adrShippingFax = $adrShippingFax;

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



  
    // TCMSFieldVarchar
public function getShopShippingGroupName(): string
{
    return $this->shopShippingGroupName;
}
public function setShopShippingGroupName(string $shopShippingGroupName): self
{
    $this->shopShippingGroupName = $shopShippingGroupName;

    return $this;
}


  
    // TCMSFieldDecimal
public function getShopShippingGroupPrice(): float
{
    return $this->shopShippingGroupPrice;
}
public function setShopShippingGroupPrice(float $shopShippingGroupPrice): self
{
    $this->shopShippingGroupPrice = $shopShippingGroupPrice;

    return $this;
}


  
    // TCMSFieldDecimal
public function getShopShippingGroupVatPercent(): float
{
    return $this->shopShippingGroupVatPercent;
}
public function setShopShippingGroupVatPercent(float $shopShippingGroupVatPercent): self
{
    $this->shopShippingGroupVatPercent = $shopShippingGroupVatPercent;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopOrderShippingGroupParameterCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderShippingGroupParameterCollection;
}
public function setShopOrderShippingGroupParameterCollection(\Doctrine\Common\Collections\Collection $shopOrderShippingGroupParameterCollection): self
{
    $this->shopOrderShippingGroupParameterCollection = $shopOrderShippingGroupParameterCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopPaymentMethod(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod|null
{
    return $this->shopPaymentMethod;
}
public function setShopPaymentMethod(\ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod|null $shopPaymentMethod): self
{
    $this->shopPaymentMethod = $shopPaymentMethod;
    $this->shopPaymentMethodId = $shopPaymentMethod?->getId();

    return $this;
}
public function getShopPaymentMethodId(): ?string
{
    return $this->shopPaymentMethodId;
}
public function setShopPaymentMethodId(?string $shopPaymentMethodId): self
{
    $this->shopPaymentMethodId = $shopPaymentMethodId;
    // todo - load new id
    //$this->shopPaymentMethodId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getShopPaymentMethodName(): string
{
    return $this->shopPaymentMethodName;
}
public function setShopPaymentMethodName(string $shopPaymentMethodName): self
{
    $this->shopPaymentMethodName = $shopPaymentMethodName;

    return $this;
}


  
    // TCMSFieldDecimal
public function getShopPaymentMethodPrice(): float
{
    return $this->shopPaymentMethodPrice;
}
public function setShopPaymentMethodPrice(float $shopPaymentMethodPrice): self
{
    $this->shopPaymentMethodPrice = $shopPaymentMethodPrice;

    return $this;
}


  
    // TCMSFieldDecimal
public function getShopPaymentMethodVatPercent(): float
{
    return $this->shopPaymentMethodVatPercent;
}
public function setShopPaymentMethodVatPercent(float $shopPaymentMethodVatPercent): self
{
    $this->shopPaymentMethodVatPercent = $shopPaymentMethodVatPercent;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopOrderPaymentMethodParameterCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderPaymentMethodParameterCollection;
}
public function setShopOrderPaymentMethodParameterCollection(\Doctrine\Common\Collections\Collection $shopOrderPaymentMethodParameterCollection): self
{
    $this->shopOrderPaymentMethodParameterCollection = $shopOrderPaymentMethodParameterCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopOrderVatCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderVatCollection;
}
public function setShopOrderVatCollection(\Doctrine\Common\Collections\Collection $shopOrderVatCollection): self
{
    $this->shopOrderVatCollection = $shopOrderVatCollection;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueArticle(): float
{
    return $this->valueArticle;
}
public function setValueArticle(float $valueArticle): self
{
    $this->valueArticle = $valueArticle;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueTotal(): float
{
    return $this->valueTotal;
}
public function setValueTotal(float $valueTotal): self
{
    $this->valueTotal = $valueTotal;

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



  
    // TCMSFieldDecimal
public function getValueWrapping(): float
{
    return $this->valueWrapping;
}
public function setValueWrapping(float $valueWrapping): self
{
    $this->valueWrapping = $valueWrapping;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueWrappingCard(): float
{
    return $this->valueWrappingCard;
}
public function setValueWrappingCard(float $valueWrappingCard): self
{
    $this->valueWrappingCard = $valueWrappingCard;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueVouchers(): float
{
    return $this->valueVouchers;
}
public function setValueVouchers(float $valueVouchers): self
{
    $this->valueVouchers = $valueVouchers;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueVouchersNotSponsored(): float
{
    return $this->valueVouchersNotSponsored;
}
public function setValueVouchersNotSponsored(float $valueVouchersNotSponsored): self
{
    $this->valueVouchersNotSponsored = $valueVouchersNotSponsored;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueDiscounts(): float
{
    return $this->valueDiscounts;
}
public function setValueDiscounts(float $valueDiscounts): self
{
    $this->valueDiscounts = $valueDiscounts;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueVatTotal(): float
{
    return $this->valueVatTotal;
}
public function setValueVatTotal(float $valueVatTotal): self
{
    $this->valueVatTotal = $valueVatTotal;

    return $this;
}


  
    // TCMSFieldDecimal
public function getCountArticles(): float
{
    return $this->countArticles;
}
public function setCountArticles(float $countArticles): self
{
    $this->countArticles = $countArticles;

    return $this;
}


  
    // TCMSFieldNumber
public function getCountUniqueArticles(): int
{
    return $this->countUniqueArticles;
}
public function setCountUniqueArticles(int $countUniqueArticles): self
{
    $this->countUniqueArticles = $countUniqueArticles;

    return $this;
}


  
    // TCMSFieldDecimal
public function getTotalweight(): float
{
    return $this->totalweight;
}
public function setTotalweight(float $totalweight): self
{
    $this->totalweight = $totalweight;

    return $this;
}


  
    // TCMSFieldDecimal
public function getTotalvolume(): float
{
    return $this->totalvolume;
}
public function setTotalvolume(float $totalvolume): self
{
    $this->totalvolume = $totalvolume;

    return $this;
}


  
    // TCMSFieldBoolean
public function isSystemOrderSaveCompleted(): bool
{
    return $this->systemOrderSaveCompleted;
}
public function setSystemOrderSaveCompleted(bool $systemOrderSaveCompleted): self
{
    $this->systemOrderSaveCompleted = $systemOrderSaveCompleted;

    return $this;
}


  
    // TCMSFieldBoolean
public function isSystemOrderNotificationSend(): bool
{
    return $this->systemOrderNotificationSend;
}
public function setSystemOrderNotificationSend(bool $systemOrderNotificationSend): self
{
    $this->systemOrderNotificationSend = $systemOrderNotificationSend;

    return $this;
}


  
    // TCMSFieldBoolean
public function isSystemOrderPaymentMethodExecuted(): bool
{
    return $this->systemOrderPaymentMethodExecuted;
}
public function setSystemOrderPaymentMethodExecuted(bool $systemOrderPaymentMethodExecuted): self
{
    $this->systemOrderPaymentMethodExecuted = $systemOrderPaymentMethodExecuted;

    return $this;
}


  
    // TCMSFieldDateTime
public function getSystemOrderPaymentMethodExecutedDate(): \DateTime|null
{
    return $this->systemOrderPaymentMethodExecutedDate;
}
public function setSystemOrderPaymentMethodExecutedDate(\DateTime|null $systemOrderPaymentMethodExecutedDate): self
{
    $this->systemOrderPaymentMethodExecutedDate = $systemOrderPaymentMethodExecutedDate;

    return $this;
}


  
    // TCMSFieldBoolean
public function isOrderIsPaid(): bool
{
    return $this->orderIsPaid;
}
public function setOrderIsPaid(bool $orderIsPaid): self
{
    $this->orderIsPaid = $orderIsPaid;

    return $this;
}


  
    // TCMSFieldDateTime
public function getOrderIsPaidDate(): \DateTime|null
{
    return $this->orderIsPaidDate;
}
public function setOrderIsPaidDate(\DateTime|null $orderIsPaidDate): self
{
    $this->orderIsPaidDate = $orderIsPaidDate;

    return $this;
}


  
    // TCMSFieldBoolean
public function isCanceled(): bool
{
    return $this->canceled;
}
public function setCanceled(bool $canceled): self
{
    $this->canceled = $canceled;

    return $this;
}


  
    // TCMSFieldDateTime
public function getCanceledDate(): \DateTime|null
{
    return $this->canceledDate;
}
public function setCanceledDate(\DateTime|null $canceledDate): self
{
    $this->canceledDate = $canceledDate;

    return $this;
}


  
    // TCMSFieldDateTime
public function getSystemOrderExportedDate(): \DateTime|null
{
    return $this->systemOrderExportedDate;
}
public function setSystemOrderExportedDate(\DateTime|null $systemOrderExportedDate): self
{
    $this->systemOrderExportedDate = $systemOrderExportedDate;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAffiliateCode(): string
{
    return $this->affiliateCode;
}
public function setAffiliateCode(string $affiliateCode): self
{
    $this->affiliateCode = $affiliateCode;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopAffiliate(): \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null
{
    return $this->pkgShopAffiliate;
}
public function setPkgShopAffiliate(\ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null $pkgShopAffiliate): self
{
    $this->pkgShopAffiliate = $pkgShopAffiliate;
    $this->pkgShopAffiliateId = $pkgShopAffiliate?->getId();

    return $this;
}
public function getPkgShopAffiliateId(): ?string
{
    return $this->pkgShopAffiliateId;
}
public function setPkgShopAffiliateId(?string $pkgShopAffiliateId): self
{
    $this->pkgShopAffiliateId = $pkgShopAffiliateId;
    // todo - load new id
    //$this->pkgShopAffiliateId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getShopVoucherUseCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopVoucherUseCollection;
}
public function setShopVoucherUseCollection(\Doctrine\Common\Collections\Collection $shopVoucherUseCollection): self
{
    $this->shopVoucherUseCollection = $shopVoucherUseCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopOrderDiscountCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderDiscountCollection;
}
public function setShopOrderDiscountCollection(\Doctrine\Common\Collections\Collection $shopOrderDiscountCollection): self
{
    $this->shopOrderDiscountCollection = $shopOrderDiscountCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopOrderStatusCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderStatusCollection;
}
public function setShopOrderStatusCollection(\Doctrine\Common\Collections\Collection $shopOrderStatusCollection): self
{
    $this->shopOrderStatusCollection = $shopOrderStatusCollection;

    return $this;
}


  
    // TCMSFieldText
public function getObjectMail(): string
{
    return $this->objectMail;
}
public function setObjectMail(string $objectMail): self
{
    $this->objectMail = $objectMail;

    return $this;
}


  
    // TCMSFieldDateTime
public function getPkgShopRatingServiceRatingProcessedOn(): \DateTime|null
{
    return $this->pkgShopRatingServiceRatingProcessedOn;
}
public function setPkgShopRatingServiceRatingProcessedOn(\DateTime|null $pkgShopRatingServiceRatingProcessedOn): self
{
    $this->pkgShopRatingServiceRatingProcessedOn = $pkgShopRatingServiceRatingProcessedOn;

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


  
    // TCMSFieldText
public function getInternalComment(): string
{
    return $this->internalComment;
}
public function setInternalComment(string $internalComment): self
{
    $this->internalComment = $internalComment;

    return $this;
}


  
    // TCMSFieldDateTime
public function getPkgShopRatingServiceOrderCompletelyShipped(): \DateTime|null
{
    return $this->pkgShopRatingServiceOrderCompletelyShipped;
}
public function setPkgShopRatingServiceOrderCompletelyShipped(\DateTime|null $pkgShopRatingServiceOrderCompletelyShipped): self
{
    $this->pkgShopRatingServiceOrderCompletelyShipped = $pkgShopRatingServiceOrderCompletelyShipped;

    return $this;
}


  
}
