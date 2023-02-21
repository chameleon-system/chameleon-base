<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopWishlist;

class PkgShopWishlistMailHistory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgShopWishlist|null - Belongs to wishlist */
private ?PkgShopWishlist $pkgShopWishlist = null
, 
    // TCMSFieldVarchar
/** @var string - Recipient name */
private string $toName = '', 
    // TCMSFieldVarchar
/** @var string - Feedback recipient (Email address) */
private string $toEmail = ''  ) {}

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
public function getToName(): string
{
    return $this->toName;
}
public function setToName(string $toName): self
{
    $this->toName = $toName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getToEmail(): string
{
    return $this->toEmail;
}
public function setToEmail(string $toEmail): self
{
    $this->toEmail = $toEmail;

    return $this;
}


  
}
