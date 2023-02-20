<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVoucherUse {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucher|null - Belongs to voucher */
private \ChameleonSystem\CoreBundle\Entity\ShopVoucher|null $shopVoucher = null,
/** @var null|string - Belongs to voucher */
private ?string $shopVoucherId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Used in this order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Used in this order */
private ?string $shopOrderId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null - Currency in which the order was made */
private \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency|null $pkgShopCurrency = null,
/** @var null|string - Currency in which the order was made */
private ?string $pkgShopCurrencyId = null
, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Used on */
private \DateTime|null $dateUsed = null, 
    // TCMSFieldDecimal
/** @var float - Value used up */
private float $valueUsed = 0, 
    // TCMSFieldDecimal
/** @var float - Value consumed in the order currency */
private float $valueUsedInOrderCurrency = 0  ) {}

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
public function getShopVoucher(): \ChameleonSystem\CoreBundle\Entity\ShopVoucher|null
{
    return $this->shopVoucher;
}
public function setShopVoucher(\ChameleonSystem\CoreBundle\Entity\ShopVoucher|null $shopVoucher): self
{
    $this->shopVoucher = $shopVoucher;
    $this->shopVoucherId = $shopVoucher?->getId();

    return $this;
}
public function getShopVoucherId(): ?string
{
    return $this->shopVoucherId;
}
public function setShopVoucherId(?string $shopVoucherId): self
{
    $this->shopVoucherId = $shopVoucherId;
    // todo - load new id
    //$this->shopVoucherId = $?->getId();

    return $this;
}



  
    // TCMSFieldDateTime
public function getDateUsed(): \DateTime|null
{
    return $this->dateUsed;
}
public function setDateUsed(\DateTime|null $dateUsed): self
{
    $this->dateUsed = $dateUsed;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValueUsed(): float
{
    return $this->valueUsed;
}
public function setValueUsed(float $valueUsed): self
{
    $this->valueUsed = $valueUsed;

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



  
    // TCMSFieldDecimal
public function getValueUsedInOrderCurrency(): float
{
    return $this->valueUsedInOrderCurrency;
}
public function setValueUsedInOrderCurrency(float $valueUsedInOrderCurrency): self
{
    $this->valueUsedInOrderCurrency = $valueUsedInOrderCurrency;

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
