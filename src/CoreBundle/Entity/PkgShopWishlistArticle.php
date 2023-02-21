<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopWishlist;
use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class PkgShopWishlistArticle {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgShopWishlist|null - Belongs to wishlist */
private ?PkgShopWishlist $pkgShopWishlist = null
, 
    // TCMSFieldVarchar
/** @var string - Amount */
private string $amount = '', 
    // TCMSFieldLookup
/** @var ShopArticle|null - Article */
private ?ShopArticle $shopArticle = null
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


  
    // TCMSFieldLookup
public function getShopArticle(): ?ShopArticle
{
    return $this->shopArticle;
}

public function setShopArticle(?ShopArticle $shopArticle): self
{
    $this->shopArticle = $shopArticle;

    return $this;
}


  
}
