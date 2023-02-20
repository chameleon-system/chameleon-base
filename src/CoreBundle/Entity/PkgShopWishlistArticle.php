<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlistArticle {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null - Belongs to wishlist */
private \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null $pkgShopWishlist = null,
/** @var null|string - Belongs to wishlist */
private ?string $pkgShopWishlistId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Article */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Article */
private ?string $shopArticleId = null
, 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldNumber
/** @var int - Amount */
private int $amount = 0, 
    // TCMSFieldText
/** @var string - Comment */
private string $comment = ''  ) {}

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



  
    // TCMSFieldDateTimeNow
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

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


  
    // TCMSFieldLookup
public function getShopArticle(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->shopArticle;
}
public function setShopArticle(\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle): self
{
    $this->shopArticle = $shopArticle;
    $this->shopArticleId = $shopArticle?->getId();

    return $this;
}
public function getShopArticleId(): ?string
{
    return $this->shopArticleId;
}
public function setShopArticleId(?string $shopArticleId): self
{
    $this->shopArticleId = $shopArticleId;
    // todo - load new id
    //$this->shopArticleId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getComment(): string
{
    return $this->comment;
}
public function setComment(string $comment): self
{
    $this->comment = $comment;

    return $this;
}


  
}
