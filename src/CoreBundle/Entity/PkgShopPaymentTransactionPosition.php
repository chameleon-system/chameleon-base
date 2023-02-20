<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentTransactionPosition {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null - Belongs to transaction */
private \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null $pkgShopPaymentTransaction = null,
/** @var null|string - Belongs to transaction */
private ?string $pkgShopPaymentTransactionId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null - Order item */
private \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $shopOrderItem = null,
/** @var null|string - Order item */
private ?string $shopOrderItemId = null
, 
    // TCMSFieldNumber
/** @var int - Amount */
private int $amount = 0, 
    // TCMSFieldDecimal
/** @var float - Value */
private float $value = 0, 
    // TCMSFieldOption
/** @var string - Type */
private string $type = 'product'  ) {}

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
public function getPkgShopPaymentTransaction(): \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null
{
    return $this->pkgShopPaymentTransaction;
}
public function setPkgShopPaymentTransaction(\ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction|null $pkgShopPaymentTransaction): self
{
    $this->pkgShopPaymentTransaction = $pkgShopPaymentTransaction;
    $this->pkgShopPaymentTransactionId = $pkgShopPaymentTransaction?->getId();

    return $this;
}
public function getPkgShopPaymentTransactionId(): ?string
{
    return $this->pkgShopPaymentTransactionId;
}
public function setPkgShopPaymentTransactionId(?string $pkgShopPaymentTransactionId): self
{
    $this->pkgShopPaymentTransactionId = $pkgShopPaymentTransactionId;
    // todo - load new id
    //$this->pkgShopPaymentTransactionId = $?->getId();

    return $this;
}



  
    // TCMSFieldNumber
public function getAmount(): int
{
    return $this->amount;
}
public function setAmount(int $amount): self
{
    $this->amount = $amount;

    return $this;
}


  
    // TCMSFieldDecimal
public function getValue(): float
{
    return $this->value;
}
public function setValue(float $value): self
{
    $this->value = $value;

    return $this;
}


  
    // TCMSFieldOption
public function getType(): string
{
    return $this->type;
}
public function setType(string $type): self
{
    $this->type = $type;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopOrderItem(): \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null
{
    return $this->shopOrderItem;
}
public function setShopOrderItem(\ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $shopOrderItem): self
{
    $this->shopOrderItem = $shopOrderItem;
    $this->shopOrderItemId = $shopOrderItem?->getId();

    return $this;
}
public function getShopOrderItemId(): ?string
{
    return $this->shopOrderItemId;
}
public function setShopOrderItemId(?string $shopOrderItemId): self
{
    $this->shopOrderItemId = $shopOrderItemId;
    // todo - load new id
    //$this->shopOrderItemId = $?->getId();

    return $this;
}



  
}
