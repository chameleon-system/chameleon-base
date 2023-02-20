<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\AmazonPaymentIdMapping;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction;
use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\ShopOrderItem;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;
use ChameleonSystem\CoreBundle\Entity\ShopOrderShippingGroupParameter;
use ChameleonSystem\CoreBundle\Entity\ShopOrderPaymentMethodParameter;
use ChameleonSystem\CoreBundle\Entity\ShopOrderVat;
use ChameleonSystem\CoreBundle\Entity\ShopVoucherUse;
use ChameleonSystem\CoreBundle\Entity\ShopOrderDiscount;
use ChameleonSystem\CoreBundle\Entity\ShopOrderStatus;

class ShopOrder {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopPaymentIpnMessage> -  */
private Collection $pkgShopPaymentIpnMessageCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, amazonPaymentIdMapping> - Amazon Pay */
private Collection $amazonPaymentIdMappingCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopPaymentTransaction> - Transactions */
private Collection $pkgShopPaymentTransactionCollection = new ArrayCollection()
, 
    // TCMSFieldLookupParentID
/** @var CmsPortal|null - Placed by portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Order number */
private string $ordernumber = '', 
    // TCMSFieldVarchar
/** @var string - Basket ID (unique ID that is already assigned in the order process) */
private string $orderIdent = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderItem> - Items */
private Collection $shopOrderItemCollection = new ArrayCollection()
, 
    // TCMSFieldLookupParentID
/** @var DataExtranetUser|null - Shop customer */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldVarchar
/** @var string - Customer number */
private string $customerNumber = '', 
    // TCMSFieldVarchar
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
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderShippingGroupParameter> - Shipping cost group – parameter/user data */
private Collection $shopOrderShippingGroupParameterCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Payment method – name */
private string $shopPaymentMethodName = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderPaymentMethodParameter> - Payment method – parameter/user data */
private Collection $shopOrderPaymentMethodParameterCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderVat> - Order VAT (by tax rate) */
private Collection $shopOrderVatCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Number of different items */
private string $countUniqueArticles = '', 
    // TCMSFieldVarchar
/** @var string - Affiliate code */
private string $affiliateCode = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopVoucherUse> - Used vouchers */
private Collection $shopVoucherUseCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderDiscount> - Discount */
private Collection $shopOrderDiscountCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderStatus> - Order status */
private Collection $shopOrderStatusCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - VAT ID */
private string $vatId = ''  ) {}

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
    // TCMSFieldLookupParentID
public function getShop(): ?Shop
{
    return $this->shop;
}

public function setShop(?Shop $shop): self
{
    $this->shop = $shop;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgShopPaymentIpnMessage>
*/
public function getPkgShopPaymentIpnMessageCollection(): Collection
{
    return $this->pkgShopPaymentIpnMessageCollection;
}

public function addPkgShopPaymentIpnMessageCollection(pkgShopPaymentIpnMessage $pkgShopPaymentIpnMessage): self
{
    if (!$this->pkgShopPaymentIpnMessageCollection->contains($pkgShopPaymentIpnMessage)) {
        $this->pkgShopPaymentIpnMessageCollection->add($pkgShopPaymentIpnMessage);
        $pkgShopPaymentIpnMessage->setShopOrder($this);
    }

    return $this;
}

public function removePkgShopPaymentIpnMessageCollection(pkgShopPaymentIpnMessage $pkgShopPaymentIpnMessage): self
{
    if ($this->pkgShopPaymentIpnMessageCollection->removeElement($pkgShopPaymentIpnMessage)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentIpnMessage->getShopOrder() === $this) {
            $pkgShopPaymentIpnMessage->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, amazonPaymentIdMapping>
*/
public function getAmazonPaymentIdMappingCollection(): Collection
{
    return $this->amazonPaymentIdMappingCollection;
}

public function addAmazonPaymentIdMappingCollection(amazonPaymentIdMapping $amazonPaymentIdMapping): self
{
    if (!$this->amazonPaymentIdMappingCollection->contains($amazonPaymentIdMapping)) {
        $this->amazonPaymentIdMappingCollection->add($amazonPaymentIdMapping);
        $amazonPaymentIdMapping->setShopOrder($this);
    }

    return $this;
}

public function removeAmazonPaymentIdMappingCollection(amazonPaymentIdMapping $amazonPaymentIdMapping): self
{
    if ($this->amazonPaymentIdMappingCollection->removeElement($amazonPaymentIdMapping)) {
        // set the owning side to null (unless already changed)
        if ($amazonPaymentIdMapping->getShopOrder() === $this) {
            $amazonPaymentIdMapping->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgShopPaymentTransaction>
*/
public function getPkgShopPaymentTransactionCollection(): Collection
{
    return $this->pkgShopPaymentTransactionCollection;
}

public function addPkgShopPaymentTransactionCollection(pkgShopPaymentTransaction $pkgShopPaymentTransaction): self
{
    if (!$this->pkgShopPaymentTransactionCollection->contains($pkgShopPaymentTransaction)) {
        $this->pkgShopPaymentTransactionCollection->add($pkgShopPaymentTransaction);
        $pkgShopPaymentTransaction->setShopOrder($this);
    }

    return $this;
}

public function removePkgShopPaymentTransactionCollection(pkgShopPaymentTransaction $pkgShopPaymentTransaction): self
{
    if ($this->pkgShopPaymentTransactionCollection->removeElement($pkgShopPaymentTransaction)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentTransaction->getShopOrder() === $this) {
            $pkgShopPaymentTransaction->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookupParentID
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
public function getOrdernumber(): string
{
    return $this->ordernumber;
}
public function setOrdernumber(string $ordernumber): self
{
    $this->ordernumber = $ordernumber;

    return $this;
}


  
    // TCMSFieldVarchar
public function getOrderIdent(): string
{
    return $this->orderIdent;
}
public function setOrderIdent(string $orderIdent): self
{
    $this->orderIdent = $orderIdent;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderItem>
*/
public function getShopOrderItemCollection(): Collection
{
    return $this->shopOrderItemCollection;
}

public function addShopOrderItemCollection(shopOrderItem $shopOrderItem): self
{
    if (!$this->shopOrderItemCollection->contains($shopOrderItem)) {
        $this->shopOrderItemCollection->add($shopOrderItem);
        $shopOrderItem->setShopOrder($this);
    }

    return $this;
}

public function removeShopOrderItemCollection(shopOrderItem $shopOrderItem): self
{
    if ($this->shopOrderItemCollection->removeElement($shopOrderItem)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderItem->getShopOrder() === $this) {
            $shopOrderItem->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderShippingGroupParameter>
*/
public function getShopOrderShippingGroupParameterCollection(): Collection
{
    return $this->shopOrderShippingGroupParameterCollection;
}

public function addShopOrderShippingGroupParameterCollection(shopOrderShippingGroupParameter $shopOrderShippingGroupParameter): self
{
    if (!$this->shopOrderShippingGroupParameterCollection->contains($shopOrderShippingGroupParameter)) {
        $this->shopOrderShippingGroupParameterCollection->add($shopOrderShippingGroupParameter);
        $shopOrderShippingGroupParameter->setShopOrder($this);
    }

    return $this;
}

public function removeShopOrderShippingGroupParameterCollection(shopOrderShippingGroupParameter $shopOrderShippingGroupParameter): self
{
    if ($this->shopOrderShippingGroupParameterCollection->removeElement($shopOrderShippingGroupParameter)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderShippingGroupParameter->getShopOrder() === $this) {
            $shopOrderShippingGroupParameter->setShopOrder(null);
        }
    }

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderPaymentMethodParameter>
*/
public function getShopOrderPaymentMethodParameterCollection(): Collection
{
    return $this->shopOrderPaymentMethodParameterCollection;
}

public function addShopOrderPaymentMethodParameterCollection(shopOrderPaymentMethodParameter $shopOrderPaymentMethodParameter): self
{
    if (!$this->shopOrderPaymentMethodParameterCollection->contains($shopOrderPaymentMethodParameter)) {
        $this->shopOrderPaymentMethodParameterCollection->add($shopOrderPaymentMethodParameter);
        $shopOrderPaymentMethodParameter->setShopOrder($this);
    }

    return $this;
}

public function removeShopOrderPaymentMethodParameterCollection(shopOrderPaymentMethodParameter $shopOrderPaymentMethodParameter): self
{
    if ($this->shopOrderPaymentMethodParameterCollection->removeElement($shopOrderPaymentMethodParameter)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderPaymentMethodParameter->getShopOrder() === $this) {
            $shopOrderPaymentMethodParameter->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderVat>
*/
public function getShopOrderVatCollection(): Collection
{
    return $this->shopOrderVatCollection;
}

public function addShopOrderVatCollection(shopOrderVat $shopOrderVat): self
{
    if (!$this->shopOrderVatCollection->contains($shopOrderVat)) {
        $this->shopOrderVatCollection->add($shopOrderVat);
        $shopOrderVat->setShopOrder($this);
    }

    return $this;
}

public function removeShopOrderVatCollection(shopOrderVat $shopOrderVat): self
{
    if ($this->shopOrderVatCollection->removeElement($shopOrderVat)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderVat->getShopOrder() === $this) {
            $shopOrderVat->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getCountUniqueArticles(): string
{
    return $this->countUniqueArticles;
}
public function setCountUniqueArticles(string $countUniqueArticles): self
{
    $this->countUniqueArticles = $countUniqueArticles;

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopVoucherUse>
*/
public function getShopVoucherUseCollection(): Collection
{
    return $this->shopVoucherUseCollection;
}

public function addShopVoucherUseCollection(shopVoucherUse $shopVoucherUse): self
{
    if (!$this->shopVoucherUseCollection->contains($shopVoucherUse)) {
        $this->shopVoucherUseCollection->add($shopVoucherUse);
        $shopVoucherUse->setShopOrder($this);
    }

    return $this;
}

public function removeShopVoucherUseCollection(shopVoucherUse $shopVoucherUse): self
{
    if ($this->shopVoucherUseCollection->removeElement($shopVoucherUse)) {
        // set the owning side to null (unless already changed)
        if ($shopVoucherUse->getShopOrder() === $this) {
            $shopVoucherUse->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderDiscount>
*/
public function getShopOrderDiscountCollection(): Collection
{
    return $this->shopOrderDiscountCollection;
}

public function addShopOrderDiscountCollection(shopOrderDiscount $shopOrderDiscount): self
{
    if (!$this->shopOrderDiscountCollection->contains($shopOrderDiscount)) {
        $this->shopOrderDiscountCollection->add($shopOrderDiscount);
        $shopOrderDiscount->setShopOrder($this);
    }

    return $this;
}

public function removeShopOrderDiscountCollection(shopOrderDiscount $shopOrderDiscount): self
{
    if ($this->shopOrderDiscountCollection->removeElement($shopOrderDiscount)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderDiscount->getShopOrder() === $this) {
            $shopOrderDiscount->setShopOrder(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderStatus>
*/
public function getShopOrderStatusCollection(): Collection
{
    return $this->shopOrderStatusCollection;
}

public function addShopOrderStatusCollection(shopOrderStatus $shopOrderStatus): self
{
    if (!$this->shopOrderStatusCollection->contains($shopOrderStatus)) {
        $this->shopOrderStatusCollection->add($shopOrderStatus);
        $shopOrderStatus->setShopOrder($this);
    }

    return $this;
}

public function removeShopOrderStatusCollection(shopOrderStatus $shopOrderStatus): self
{
    if ($this->shopOrderStatusCollection->removeElement($shopOrderStatus)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderStatus->getShopOrder() === $this) {
            $shopOrderStatus->setShopOrder(null);
        }
    }

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


  
}
