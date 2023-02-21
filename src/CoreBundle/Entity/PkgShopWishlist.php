<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;
use ChameleonSystem\CoreBundle\Entity\PkgShopWishlistArticle;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\PkgShopWishlistMailHistory;

class PkgShopWishlist {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var DataExtranetUser|null - Belongs to user */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopWishlistArticle> - Wishlist articles */
private Collection $pkgShopWishlistArticleCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgShopWishlistMailHistory> - Wishlist mail history */
private Collection $pkgShopWishlistMailHistoryCollection = new ArrayCollection()
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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgShopWishlistArticle>
*/
public function getPkgShopWishlistArticleCollection(): Collection
{
    return $this->pkgShopWishlistArticleCollection;
}

public function addPkgShopWishlistArticleCollection(pkgShopWishlistArticle $pkgShopWishlistArticle): self
{
    if (!$this->pkgShopWishlistArticleCollection->contains($pkgShopWishlistArticle)) {
        $this->pkgShopWishlistArticleCollection->add($pkgShopWishlistArticle);
        $pkgShopWishlistArticle->setPkgShopWishlist($this);
    }

    return $this;
}

public function removePkgShopWishlistArticleCollection(pkgShopWishlistArticle $pkgShopWishlistArticle): self
{
    if ($this->pkgShopWishlistArticleCollection->removeElement($pkgShopWishlistArticle)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopWishlistArticle->getPkgShopWishlist() === $this) {
            $pkgShopWishlistArticle->setPkgShopWishlist(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgShopWishlistMailHistory>
*/
public function getPkgShopWishlistMailHistoryCollection(): Collection
{
    return $this->pkgShopWishlistMailHistoryCollection;
}

public function addPkgShopWishlistMailHistoryCollection(pkgShopWishlistMailHistory $pkgShopWishlistMailHistory): self
{
    if (!$this->pkgShopWishlistMailHistoryCollection->contains($pkgShopWishlistMailHistory)) {
        $this->pkgShopWishlistMailHistoryCollection->add($pkgShopWishlistMailHistory);
        $pkgShopWishlistMailHistory->setPkgShopWishlist($this);
    }

    return $this;
}

public function removePkgShopWishlistMailHistoryCollection(pkgShopWishlistMailHistory $pkgShopWishlistMailHistory): self
{
    if ($this->pkgShopWishlistMailHistoryCollection->removeElement($pkgShopWishlistMailHistory)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopWishlistMailHistory->getPkgShopWishlist() === $this) {
            $pkgShopWishlistMailHistory->setPkgShopWishlist(null);
        }
    }

    return $this;
}


  
}
