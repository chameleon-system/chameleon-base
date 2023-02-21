<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction;
use ChameleonSystem\CoreBundle\Entity\ShopOrderItem;

class PkgShopPaymentTransactionPosition {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgShopPaymentTransaction|null - Belongs to transaction */
private ?PkgShopPaymentTransaction $pkgShopPaymentTransaction = null
, 
    // TCMSFieldVarchar
/** @var string - Amount */
private string $amount = '', 
    // TCMSFieldLookup
/** @var ShopOrderItem|null - Order item */
private ?ShopOrderItem $shopOrderItem = null
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
public function getPkgShopPaymentTransaction(): ?PkgShopPaymentTransaction
{
    return $this->pkgShopPaymentTransaction;
}

public function setPkgShopPaymentTransaction(?PkgShopPaymentTransaction $pkgShopPaymentTransaction): self
{
    $this->pkgShopPaymentTransaction = $pkgShopPaymentTransaction;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAmount(): string
{
    return $this->amount;
}
public function setAmount(string $amount): self
{
    $this->amount = $amount;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopOrderItem(): ?ShopOrderItem
{
    return $this->shopOrderItem;
}

public function setShopOrderItem(?ShopOrderItem $shopOrderItem): self
{
    $this->shopOrderItem = $shopOrderItem;

    return $this;
}


  
}
