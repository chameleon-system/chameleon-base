<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopWishlist;
use ChameleonSystem\CoreBundle\Entity\ShopOrderItem;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;

class PkgShopWishlistOrderItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgShopWishlist|null - Wishlist */
private ?PkgShopWishlist $pkgShopWishlist = null
, 
    // TCMSFieldLookup
/** @var ShopOrderItem|null - Order item */
private ?ShopOrderItem $shopOrderItem = null
, 
    // TCMSFieldLookup
/** @var DataExtranetUser|null - Wishlist owner */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldVarchar
/** @var string - Email of the wishlist owner */
private string $dataExtranetUserEmail = ''  ) {}

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
public function getPkgShopWishlist(): ?PkgShopWishlist
{
    return $this->pkgShopWishlist;
}

public function setPkgShopWishlist(?PkgShopWishlist $pkgShopWishlist): self
{
    $this->pkgShopWishlist = $pkgShopWishlist;

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


  
    // TCMSFieldVarchar
public function getDataExtranetUserEmail(): string
{
    return $this->dataExtranetUserEmail;
}
public function setDataExtranetUserEmail(string $dataExtranetUserEmail): self
{
    $this->dataExtranetUserEmail = $dataExtranetUserEmail;

    return $this;
}


  
}
