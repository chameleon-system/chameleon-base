<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlist {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Belongs to user */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Belongs to user */
private ?string $dataExtranetUserId = null
, 
    // TCMSFieldText
/** @var string - Description stored by the user */
private string $description = '', 
    // TCMSFieldBoolean
/** @var bool - Public */
private bool $isPublic = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlistArticle[] - Wishlist articles */
private \Doctrine\Common\Collections\Collection $pkgShopWishlistArticleCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlistMailHistory[] - Wishlist mail history */
private \Doctrine\Common\Collections\Collection $pkgShopWishlistMailHistoryCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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



  
    // TCMSFieldText
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsPublic(): bool
{
    return $this->isPublic;
}
public function setIsPublic(bool $isPublic): self
{
    $this->isPublic = $isPublic;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopWishlistArticleCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopWishlistArticleCollection;
}
public function setPkgShopWishlistArticleCollection(\Doctrine\Common\Collections\Collection $pkgShopWishlistArticleCollection): self
{
    $this->pkgShopWishlistArticleCollection = $pkgShopWishlistArticleCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopWishlistMailHistoryCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopWishlistMailHistoryCollection;
}
public function setPkgShopWishlistMailHistoryCollection(\Doctrine\Common\Collections\Collection $pkgShopWishlistMailHistoryCollection): self
{
    $this->pkgShopWishlistMailHistoryCollection = $pkgShopWishlistMailHistoryCollection;

    return $this;
}


  
}
