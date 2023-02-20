<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlistMailHistory {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null - Belongs to wishlist */
private \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist|null $pkgShopWishlist = null,
/** @var null|string - Belongs to wishlist */
private ?string $pkgShopWishlistId = null
, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Email sent on */
private \DateTime|null $datesend = null, 
    // TCMSFieldVarchar
/** @var string - Recipient name */
private string $toName = '', 
    // TCMSFieldEmail
/** @var string - Feedback recipient (Email address) */
private string $toEmail = '', 
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



  
    // TCMSFieldDateTime
public function getDatesend(): \DateTime|null
{
    return $this->datesend;
}
public function setDatesend(\DateTime|null $datesend): self
{
    $this->datesend = $datesend;

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


  
    // TCMSFieldEmail
public function getToEmail(): string
{
    return $this->toEmail;
}
public function setToEmail(string $toEmail): self
{
    $this->toEmail = $toEmail;

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
