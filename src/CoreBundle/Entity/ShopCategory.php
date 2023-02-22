<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\ShopVat;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\PkgShopListfilter;
use ChameleonSystem\CoreBundle\Entity\ShopCategoryTab;

class ShopCategory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopCategory|null - Subcategory of */
private ?ShopCategory $shopCategory = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Icon for navigation */
private ?CmsMedia $naviIconCmsMedia = null
, 
    // TCMSFieldVarchar
/** @var string - Category name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Additional product name */
private string $nameProduct = '', 
    // TCMSFieldVarchar
/** @var string - SEO pattern */
private string $seoPattern = '', 
    // TCMSFieldLookup
/** @var ShopVat|null - VAT group */
private ?ShopVat $shopVat = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Category image */
private ?CmsMedia $im = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopCategory> - Subcategories */
private Collection $shopCategoryCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Meta keywords */
private string $metaKeywords = '', 
    // TCMSFieldLookup
/** @var PkgShopListfilter|null - List filter for the category */
private ?PkgShopListfilter $pkgShopListfilter = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopCategoryTab> - Category */
private Collection $shopCategoryTabCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Meta description */
private string $metaDescription = ''  ) {}

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
public function getShopCategory(): ?ShopCategory
{
    return $this->shopCategory;
}

public function setShopCategory(?ShopCategory $shopCategory): self
{
    $this->shopCategory = $shopCategory;

    return $this;
}


  
    // TCMSFieldLookup
public function getNaviIconCmsMedia(): ?CmsMedia
{
    return $this->naviIconCmsMedia;
}

public function setNaviIconCmsMedia(?CmsMedia $naviIconCmsMedia): self
{
    $this->naviIconCmsMedia = $naviIconCmsMedia;

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
public function getNameProduct(): string
{
    return $this->nameProduct;
}
public function setNameProduct(string $nameProduct): self
{
    $this->nameProduct = $nameProduct;

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
public function getIm(): ?CmsMedia
{
    return $this->im;
}

public function setIm(?CmsMedia $im): self
{
    $this->im = $im;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopCategory>
*/
public function getShopCategoryCollection(): Collection
{
    return $this->shopCategoryCollection;
}

public function addShopCategoryCollection(ShopCategory $shopCategory): self
{
    if (!$this->shopCategoryCollection->contains($shopCategory)) {
        $this->shopCategoryCollection->add($shopCategory);
        $shopCategory->setShopCategory($this);
    }

    return $this;
}

public function removeShopCategoryCollection(ShopCategory $shopCategory): self
{
    if ($this->shopCategoryCollection->removeElement($shopCategory)) {
        // set the owning side to null (unless already changed)
        if ($shopCategory->getShopCategory() === $this) {
            $shopCategory->setShopCategory(null);
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


  
    // TCMSFieldLookup
public function getPkgShopListfilter(): ?PkgShopListfilter
{
    return $this->pkgShopListfilter;
}

public function setPkgShopListfilter(?PkgShopListfilter $pkgShopListfilter): self
{
    $this->pkgShopListfilter = $pkgShopListfilter;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopCategoryTab>
*/
public function getShopCategoryTabCollection(): Collection
{
    return $this->shopCategoryTabCollection;
}

public function addShopCategoryTabCollection(ShopCategoryTab $shopCategoryTab): self
{
    if (!$this->shopCategoryTabCollection->contains($shopCategoryTab)) {
        $this->shopCategoryTabCollection->add($shopCategoryTab);
        $shopCategoryTab->setShopCategory($this);
    }

    return $this;
}

public function removeShopCategoryTabCollection(ShopCategoryTab $shopCategoryTab): self
{
    if ($this->shopCategoryTabCollection->removeElement($shopCategoryTab)) {
        // set the owning side to null (unless already changed)
        if ($shopCategoryTab->getShopCategory() === $this) {
            $shopCategoryTab->setShopCategory(null);
        }
    }

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


  
}
