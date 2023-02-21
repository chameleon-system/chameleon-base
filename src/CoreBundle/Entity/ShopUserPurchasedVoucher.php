<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;
use ChameleonSystem\CoreBundle\Entity\ShopVoucher;

class ShopUserPurchasedVoucher {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var DataExtranetUser|null - Belongs to customer */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldLookup
/** @var ShopVoucher|null - Voucher */
private ?ShopVoucher $shopVoucher = null
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
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

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


  
}
