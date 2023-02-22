<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticleImage;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\ShopArticlePreviewImage;
use ChameleonSystem\CoreBundle\Entity\ShopArticleDocument;
use ChameleonSystem\CoreBundle\Entity\ShopManufacturer;
use ChameleonSystem\CoreBundle\Entity\ShopVat;
use ChameleonSystem\CoreBundle\Entity\ShopUnitOfMeasurement;
use ChameleonSystem\CoreBundle\Entity\ShopArticleStock;
use ChameleonSystem\CoreBundle\Entity\ShopStockMessage;
use ChameleonSystem\CoreBundle\Entity\ShopCategory;
use ChameleonSystem\CoreBundle\Entity\ShopArticleContributor;
use ChameleonSystem\CoreBundle\Entity\ShopArticleReview;
use ChameleonSystem\CoreBundle\Entity\ShopBundleArticle;
use ChameleonSystem\CoreBundle\Entity\ShopVariantSet;
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
/** @var Collection<int, ShopArticleImage> - Detailed product pictures */
private Collection $shopArticleImageCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Default preview image of the product */
private ?CmsMedia $cmsMediaDefaultPreviewImage = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticlePreviewImage> - Product preview images */
private Collection $shopArticlePreviewImageCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticleDocument> - Product documents */
private Collection $shopArticleDocumentCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Quantifier / Product ranking */
private string $listRank = '', 
    // TCMSFieldLookup
/** @var ShopManufacturer|null - Manufacturer / Brand */
private ?ShopManufacturer $shopManufacturer = null
, 
    // TCMSFieldLookup
/** @var ShopVat|null - VAT group */
private ?ShopVat $shopVat = null
, 
    // TCMSFieldLookup
/** @var ShopUnitOfMeasurement|null - Measurement unit of content */
private ?ShopUnitOfMeasurement $shopUnitOfMeasurement = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticleStock> - Stock */
private Collection $shopArticleStockCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var ShopStockMessage|null - Delivery status */
private ?ShopStockMessage $shopStockMessage = null
, 
    // TCMSFieldLookup
/** @var ShopCategory|null - Main category of the product */
private ?ShopCategory $shopCategory = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticleContributor> - Contributing persons */
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
/** @var Collection<int, ShopArticleReview> - Customer reviews */
private Collection $shopArticleReviewCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopBundleArticle> - Items belonging to this bundle */
private Collection $shopBundleArticleCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Variant name */
private string $nameVariantInfo = '', 
    // TCMSFieldLookup
/** @var ShopVariantSet|null - Variant set */
private ?ShopVariantSet $shopVariantSet = null
, 
    // TCMSFieldLookup
/** @var ShopArticle|null - Is a variant of */
private ?ShopArticle $variantParent = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticle> - Product variants */
private Collection $shopArticleVariantsCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Meta keywords */
private string $metaKeywords = '', 
    // TCMSFieldVarchar
/** @var string - Meta description */
private string $metaDescription = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticleStats> - Statistics */
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
* @return Collection<int, ShopArticleImage>
*/
public function getShopArticleImageCollection(): Collection
{
    return $this->shopArticleImageCollection;
}

public function addShopArticleImageCollection(ShopArticleImage $shopArticleImage): self
{
    if (!$this->shopArticleImageCollection->contains($shopArticleImage)) {
        $this->shopArticleImageCollection->add($shopArticleImage);
        $shopArticleImage->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleImageCollection(ShopArticleImage $shopArticleImage): self
{
    if ($this->shopArticleImageCollection->removeElement($shopArticleImage)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleImage->getShopArticle() === $this) {
            $shopArticleImage->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMediaDefaultPreviewImage(): ?CmsMedia
{
    return $this->cmsMediaDefaultPreviewImage;
}

public function setCmsMediaDefaultPreviewImage(?CmsMedia $cmsMediaDefaultPreviewImage): self
{
    $this->cmsMediaDefaultPreviewImage = $cmsMediaDefaultPreviewImage;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopArticlePreviewImage>
*/
public function getShopArticlePreviewImageCollection(): Collection
{
    return $this->shopArticlePreviewImageCollection;
}

public function addShopArticlePreviewImageCollection(ShopArticlePreviewImage $shopArticlePreviewImage): self
{
    if (!$this->shopArticlePreviewImageCollection->contains($shopArticlePreviewImage)) {
        $this->shopArticlePreviewImageCollection->add($shopArticlePreviewImage);
        $shopArticlePreviewImage->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticlePreviewImageCollection(ShopArticlePreviewImage $shopArticlePreviewImage): self
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
* @return Collection<int, ShopArticleDocument>
*/
public function getShopArticleDocumentCollection(): Collection
{
    return $this->shopArticleDocumentCollection;
}

public function addShopArticleDocumentCollection(ShopArticleDocument $shopArticleDocument): self
{
    if (!$this->shopArticleDocumentCollection->contains($shopArticleDocument)) {
        $this->shopArticleDocumentCollection->add($shopArticleDocument);
        $shopArticleDocument->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleDocumentCollection(ShopArticleDocument $shopArticleDocument): self
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


  
    // TCMSFieldLookup
public function getShopManufacturer(): ?ShopManufacturer
{
    return $this->shopManufacturer;
}

public function setShopManufacturer(?ShopManufacturer $shopManufacturer): self
{
    $this->shopManufacturer = $shopManufacturer;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVat(): ?ShopVat
{
    return $this->shopVat;
}

public function setShopVat(?ShopVat $shopVat): self
{
    $this->shopVat = $shopVat;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopUnitOfMeasurement(): ?ShopUnitOfMeasurement
{
    return $this->shopUnitOfMeasurement;
}

public function setShopUnitOfMeasurement(?ShopUnitOfMeasurement $shopUnitOfMeasurement): self
{
    $this->shopUnitOfMeasurement = $shopUnitOfMeasurement;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopArticleStock>
*/
public function getShopArticleStockCollection(): Collection
{
    return $this->shopArticleStockCollection;
}

public function addShopArticleStockCollection(ShopArticleStock $shopArticleStock): self
{
    if (!$this->shopArticleStockCollection->contains($shopArticleStock)) {
        $this->shopArticleStockCollection->add($shopArticleStock);
        $shopArticleStock->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleStockCollection(ShopArticleStock $shopArticleStock): self
{
    if ($this->shopArticleStockCollection->removeElement($shopArticleStock)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleStock->getShopArticle() === $this) {
            $shopArticleStock->setShopArticle(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
public function getShopStockMessage(): ?ShopStockMessage
{
    return $this->shopStockMessage;
}

public function setShopStockMessage(?ShopStockMessage $shopStockMessage): self
{
    $this->shopStockMessage = $shopStockMessage;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopCategory(): ?ShopCategory
{
    return $this->shopCategory;
}

public function setShopCategory(?ShopCategory $shopCategory): self
{
    $this->shopCategory = $shopCategory;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopArticleContributor>
*/
public function getShopArticleContributorCollection(): Collection
{
    return $this->shopArticleContributorCollection;
}

public function addShopArticleContributorCollection(ShopArticleContributor $shopArticleContributor): self
{
    if (!$this->shopArticleContributorCollection->contains($shopArticleContributor)) {
        $this->shopArticleContributorCollection->add($shopArticleContributor);
        $shopArticleContributor->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleContributorCollection(ShopArticleContributor $shopArticleContributor): self
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
* @return Collection<int, ShopArticleReview>
*/
public function getShopArticleReviewCollection(): Collection
{
    return $this->shopArticleReviewCollection;
}

public function addShopArticleReviewCollection(ShopArticleReview $shopArticleReview): self
{
    if (!$this->shopArticleReviewCollection->contains($shopArticleReview)) {
        $this->shopArticleReviewCollection->add($shopArticleReview);
        $shopArticleReview->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleReviewCollection(ShopArticleReview $shopArticleReview): self
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
* @return Collection<int, ShopBundleArticle>
*/
public function getShopBundleArticleCollection(): Collection
{
    return $this->shopBundleArticleCollection;
}

public function addShopBundleArticleCollection(ShopBundleArticle $shopBundleArticle): self
{
    if (!$this->shopBundleArticleCollection->contains($shopBundleArticle)) {
        $this->shopBundleArticleCollection->add($shopBundleArticle);
        $shopBundleArticle->setShopArticle($this);
    }

    return $this;
}

public function removeShopBundleArticleCollection(ShopBundleArticle $shopBundleArticle): self
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


  
    // TCMSFieldLookup
public function getShopVariantSet(): ?ShopVariantSet
{
    return $this->shopVariantSet;
}

public function setShopVariantSet(?ShopVariantSet $shopVariantSet): self
{
    $this->shopVariantSet = $shopVariantSet;

    return $this;
}


  
    // TCMSFieldLookup
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
* @return Collection<int, ShopArticle>
*/
public function getShopArticleVariantsCollection(): Collection
{
    return $this->shopArticleVariantsCollection;
}

public function addShopArticleVariantsCollection(ShopArticle $shopArticleVariants): self
{
    if (!$this->shopArticleVariantsCollection->contains($shopArticleVariants)) {
        $this->shopArticleVariantsCollection->add($shopArticleVariants);
        $shopArticleVariants->setVariantParent($this);
    }

    return $this;
}

public function removeShopArticleVariantsCollection(ShopArticle $shopArticleVariants): self
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
* @return Collection<int, ShopArticleStats>
*/
public function getShopArticleStatsCollection(): Collection
{
    return $this->shopArticleStatsCollection;
}

public function addShopArticleStatsCollection(ShopArticleStats $shopArticleStats): self
{
    if (!$this->shopArticleStatsCollection->contains($shopArticleStats)) {
        $this->shopArticleStatsCollection->add($shopArticleStats);
        $shopArticleStats->setShopArticle($this);
    }

    return $this;
}

public function removeShopArticleStatsCollection(ShopArticleStats $shopArticleStats): self
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
