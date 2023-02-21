<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopVoucher;
use ChameleonSystem\CoreBundle\Entity\ShopOrder;
use ChameleonSystem\CoreBundle\Entity\PkgShopCurrency;

class ShopVoucherUse {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopVoucher|null - Belongs to voucher */
private ?ShopVoucher $shopVoucher = null
, 
    // TCMSFieldLookup
/** @var ShopOrder|null - Used in this order */
private ?ShopOrder $shopOrder = null
, 
    // TCMSFieldLookup
/** @var PkgShopCurrency|null - Currency in which the order was made */
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
public function getShopVoucher(): ?ShopVoucher
{
    return $this->shopVoucher;
}

public function setShopVoucher(?ShopVoucher $shopVoucher): self
{
    $this->shopVoucher = $shopVoucher;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopOrder(): ?ShopOrder
{
    return $this->shopOrder;
}

public function setShopOrder(?ShopOrder $shopOrder): self
{
    $this->shopOrder = $shopOrder;

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
