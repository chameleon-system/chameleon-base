<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticleImage;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\ShopArticlePreviewImage;
use ChameleonSystem\CoreBundle\Entity\ShopArticleDocument;
use ChameleonSystem\CoreBundle\Entity\ShopArticleStock;
use ChameleonSystem\CoreBundle\Entity\ShopArticleContributor;
use ChameleonSystem\CoreBundle\Entity\ShopArticleReview;
use ChameleonSystem\CoreBundle\Entity\ShopBundleArticle;
use ChameleonSystem\CoreBundle\Entity\ShopArticleStats;

class ShopArticle {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - SEO pattern */
private string $seoPattern = '', 
    // TCMSFieldVarchar
/** @var string - Product number */
private string $articlenumber = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticleImage> - Detailed product pictures */
private Collection $shopArticleImageCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticlePreviewImage> - Product preview images */
private Collection $shopArticlePreviewImageCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticleDocument> - Product documents */
private Collection $shopArticleDocumentCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Quantifier / Product ranking */
private string $listRank = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticleStock> - Stock */
private Collection $shopArticleStockCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticleContributor> - Contributing persons */
private Collection $shopArticleContributorCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Subtitle */
private string $subtitle = '', 
    // TCMSFieldVarchar
/** @var string - USP */
private string $usp = '', 
    // TCMSFieldVarchar
/** @var string - Number of stars */
private string $stars = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticleReview> - Customer reviews */
private Collection $shopArticleReviewCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopBundleArticle> - Items belonging to this bundle */
private Collection $shopBundleArticleCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Variant name */
private string $nameVariantInfo = '', 
    // TCMSFieldLookupParentID
/** @var ShopArticle|null - Is a variant of */
private ?ShopArticle $variantParent = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticle> - Product variants */
private Collection $shopArticleVariantsCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Meta keywords */
private string $metaKeywords = '', 
    // TCMSFieldVarchar
/** @var string - Meta description */
private string $metaDescription = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopArticleStats> - Statistics */
private Collection $shopArticleStatsCollection = new ArrayCollection()
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
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSeoPattern(): string
{
    return $this->seoPattern;
}
public function setSeoPattern(string $seoPattern): self
{
    $this->seoPattern = $seoPattern;

    return $this;
}


  
    // TCMSFieldVarchar
public function getArticlenumber(): string
{
    return $this->articlenumber;
}
public function setArticlenumber(string $articlenumber): self
{
    $this->articlenumber = $articlenumber;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticleImage>
*/
public function getShopArticleImageCollection(): Collection
{
    return $this->shopArticleImageCollection;
}

public function addShopArticleImageCollection(shopArticleImage $shopArticleImage): self
{
    if (!$this->shopArticleImageCollection->contains($shopArticleImage)) {
        $this->shopArticleImageCollection->add($shopArticleImage);
        $shopArticleImage->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleImageCollection(shopArticleImage $shopArticleImage): self
{
    if ($this->shopArticleImageCollection->removeElement($shopArticleImage)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleImage->getShopArticle() === $this) {
            $shopArticleImage->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticlePreviewImage>
*/
public function getShopArticlePreviewImageCollection(): Collection
{
    return $this->shopArticlePreviewImageCollection;
}

public function addShopArticlePreviewImageCollection(shopArticlePreviewImage $shopArticlePreviewImage): self
{
    if (!$this->shopArticlePreviewImageCollection->contains($shopArticlePreviewImage)) {
        $this->shopArticlePreviewImageCollection->add($shopArticlePreviewImage);
        $shopArticlePreviewImage->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticlePreviewImageCollection(shopArticlePreviewImage $shopArticlePreviewImage): self
{
    if ($this->shopArticlePreviewImageCollection->removeElement($shopArticlePreviewImage)) {
        // set the owning side to null (unless already changed)
        if ($shopArticlePreviewImage->getShopArticle() === $this) {
            $shopArticlePreviewImage->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticleDocument>
*/
public function getShopArticleDocumentCollection(): Collection
{
    return $this->shopArticleDocumentCollection;
}

public function addShopArticleDocumentCollection(shopArticleDocument $shopArticleDocument): self
{
    if (!$this->shopArticleDocumentCollection->contains($shopArticleDocument)) {
        $this->shopArticleDocumentCollection->add($shopArticleDocument);
        $shopArticleDocument->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleDocumentCollection(shopArticleDocument $shopArticleDocument): self
{
    if ($this->shopArticleDocumentCollection->removeElement($shopArticleDocument)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleDocument->getShopArticle() === $this) {
            $shopArticleDocument->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getListRank(): string
{
    return $this->listRank;
}
public function setListRank(string $listRank): self
{
    $this->listRank = $listRank;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticleStock>
*/
public function getShopArticleStockCollection(): Collection
{
    return $this->shopArticleStockCollection;
}

public function addShopArticleStockCollection(shopArticleStock $shopArticleStock): self
{
    if (!$this->shopArticleStockCollection->contains($shopArticleStock)) {
        $this->shopArticleStockCollection->add($shopArticleStock);
        $shopArticleStock->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleStockCollection(shopArticleStock $shopArticleStock): self
{
    if ($this->shopArticleStockCollection->removeElement($shopArticleStock)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleStock->getShopArticle() === $this) {
            $shopArticleStock->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticleContributor>
*/
public function getShopArticleContributorCollection(): Collection
{
    return $this->shopArticleContributorCollection;
}

public function addShopArticleContributorCollection(shopArticleContributor $shopArticleContributor): self
{
    if (!$this->shopArticleContributorCollection->contains($shopArticleContributor)) {
        $this->shopArticleContributorCollection->add($shopArticleContributor);
        $shopArticleContributor->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleContributorCollection(shopArticleContributor $shopArticleContributor): self
{
    if ($this->shopArticleContributorCollection->removeElement($shopArticleContributor)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleContributor->getShopArticle() === $this) {
            $shopArticleContributor->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getSubtitle(): string
{
    return $this->subtitle;
}
public function setSubtitle(string $subtitle): self
{
    $this->subtitle = $subtitle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUsp(): string
{
    return $this->usp;
}
public function setUsp(string $usp): self
{
    $this->usp = $usp;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStars(): string
{
    return $this->stars;
}
public function setStars(string $stars): self
{
    $this->stars = $stars;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticleReview>
*/
public function getShopArticleReviewCollection(): Collection
{
    return $this->shopArticleReviewCollection;
}

public function addShopArticleReviewCollection(shopArticleReview $shopArticleReview): self
{
    if (!$this->shopArticleReviewCollection->contains($shopArticleReview)) {
        $this->shopArticleReviewCollection->add($shopArticleReview);
        $shopArticleReview->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleReviewCollection(shopArticleReview $shopArticleReview): self
{
    if ($this->shopArticleReviewCollection->removeElement($shopArticleReview)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleReview->getShopArticle() === $this) {
            $shopArticleReview->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopBundleArticle>
*/
public function getShopBundleArticleCollection(): Collection
{
    return $this->shopBundleArticleCollection;
}

public function addShopBundleArticleCollection(shopBundleArticle $shopBundleArticle): self
{
    if (!$this->shopBundleArticleCollection->contains($shopBundleArticle)) {
        $this->shopBundleArticleCollection->add($shopBundleArticle);
        $shopBundleArticle->setShopArticle($this);
    }

    return $this;
}

public function removeShopBundleArticleCollection(shopBundleArticle $shopBundleArticle): self
{
    if ($this->shopBundleArticleCollection->removeElement($shopBundleArticle)) {
        // set the owning side to null (unless already changed)
        if ($shopBundleArticle->getShopArticle() === $this) {
            $shopBundleArticle->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getNameVariantInfo(): string
{
    return $this->nameVariantInfo;
}
public function setNameVariantInfo(string $nameVariantInfo): self
{
    $this->nameVariantInfo = $nameVariantInfo;

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getVariantParent(): ?ShopArticle
{
    return $this->variantParent;
}

public function setVariantParent(?ShopArticle $variantParent): self
{
    $this->variantParent = $variantParent;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticle>
*/
public function getShopArticleVariantsCollection(): Collection
{
    return $this->shopArticleVariantsCollection;
}

public function addShopArticleVariantsCollection(shopArticle $shopArticleVariants): self
{
    if (!$this->shopArticleVariantsCollection->contains($shopArticleVariants)) {
        $this->shopArticleVariantsCollection->add($shopArticleVariants);
        $shopArticleVariants->setVariantParent($this);
    }

    return $this;
}

public function removeShopArticleVariantsCollection(shopArticle $shopArticleVariants): self
{
    if ($this->shopArticleVariantsCollection->removeElement($shopArticleVariants)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleVariants->getVariantParent() === $this) {
            $shopArticleVariants->setVariantParent(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaKeywords(): string
{
    return $this->metaKeywords;
}
public function setMetaKeywords(string $metaKeywords): self
{
    $this->metaKeywords = $metaKeywords;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaDescription(): string
{
    return $this->metaDescription;
}
public function setMetaDescription(string $metaDescription): self
{
    $this->metaDescription = $metaDescription;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopArticleStats>
*/
public function getShopArticleStatsCollection(): Collection
{
    return $this->shopArticleStatsCollection;
}

public function addShopArticleStatsCollection(shopArticleStats $shopArticleStats): self
{
    if (!$this->shopArticleStatsCollection->contains($shopArticleStats)) {
        $this->shopArticleStatsCollection->add($shopArticleStats);
        $shopArticleStats->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleStatsCollection(shopArticleStats $shopArticleStats): self
{
    if ($this->shopArticleStatsCollection->removeElement($shopArticleStats)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleStats->getShopArticle() === $this) {
            $shopArticleStats->setShopArticle(null);
        }
    }

    return $this;
}


  
}
