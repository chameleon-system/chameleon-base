<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlistOrderItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null - Wishlist */
private \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null $pkgShopWishlist = null,
/** @var null|string - Wishlist */
private ?string $pkgShopWishlistId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null - Order item */
private \ChameleonSystem\CoreBundle\Entity\ShopOrderItem|null $shopOrderItem = null,
/** @var null|string - Order item */
private ?string $shopOrderItemId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Wishlist owner */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Wishlist owner */
private ?string $dataExtranetUserId = null
, 
    // TCMSFieldVarchar
/** @var string - Email of the wishlist owner */
private string $dataExtranetUserEmail = ''  ) {}

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
public function getPkgShopWishlist(): \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null
{
    return $this->pkgShopWishlist;
}
public function setPkgShopWishlist(\ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null $pkgShopWishlist): self
{
    $this->pkgShopWishlist = $pkgShopWishlist;
    $this->pkgShopWishlistId = $pkgShopWishlist?->getId();

    return $this;
}
public function getPkgShopWishlistId(): ?string
{
    return $this->pkgShopWishlistId;
}
public function setPkgShopWishlistId(?string $pkgShopWishlistId): self
{
    $this->pkgShopWishlistId = $pkgShopWishlistId;
    // todo - load new id
    //$this->pkgShopWishlistId = $?->getId();

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
