<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopVoucher;
use ChameleonSystem\CoreBundle\Entity\ShopOrder;

class ShopVoucherUse {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopVoucher|null - Belongs to voucher */
private ?ShopVoucher $shopVoucher = null
, 
    // TCMSFieldLookupParentID
/** @var ShopOrder|null - Used in this order */
private ?ShopOrder $shopOrder = null
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
    // TCMSFieldLookupParentID
public function getShopVoucher(): ?ShopVoucher
{
    return $this->shopVoucher;
}

public function setShopVoucher(?ShopVoucher $shopVoucher): self
{
    $this->shopVoucher = $shopVoucher;

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getShopOrder(): ?ShopOrder
{
    return $this->shopOrder;
}

public function setShopOrder(?ShopOrder $shopOrder): self
{
    $this->shopOrder = $shopOrder;

    return $this;
}


  
}
