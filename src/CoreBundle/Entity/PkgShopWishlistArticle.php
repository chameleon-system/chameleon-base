<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopWishlist;

class PkgShopWishlistArticle {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var PkgShopWishlist|null - Belongs to wishlist */
private ?PkgShopWishlist $pkgShopWishlist = null
, 
    // TCMSFieldVarchar
/** @var string - Amount */
private string $amount = ''  ) {}

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
public function getPkgShopWishlist(): ?PkgShopWishlist
{
    return $this->pkgShopWishlist;
}

public function setPkgShopWishlist(?PkgShopWishlist $pkgShopWishlist): self
{
    $this->pkgShopWishlist = $pkgShopWishlist;

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


  
}
