<?php
namespace ChameleonSystem\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\ShopCategoryTab;

class ShopCategory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopCategory|null - Subcategory of */
private ?ShopCategory $shopCategory = null
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
    // TCMSFieldPropertyTable
/** @var Collection<int, shopCategory> - Subcategories */
private Collection $shopCategoryCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Meta keywords */
private string $metaKeywords = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopCategoryTab> - Category */
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
    // TCMSFieldLookupParentID
public function getShopCategory(): ?ShopCategory
{
    return $this->shopCategory;
}

public function setShopCategory(?ShopCategory $shopCategory): self
{
    $this->shopCategory = $shopCategory;

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopCategory>
*/
public function getShopCategoryCollection(): Collection
{
    return $this->shopCategoryCollection;
}

public function addShopCategoryCollection(shopCategory $shopCategory): self
{
    if (!$this->shopCategoryCollection->contains($shopCategory)) {
        $this->shopCategoryCollection->add($shopCategory);
        $shopCategory->setShopCategory($this);
    }

    return $this;
}

public function removeShopCategoryCollection(shopCategory $shopCategory): self
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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopCategoryTab>
*/
public function getShopCategoryTabCollection(): Collection
{
    return $this->shopCategoryTabCollection;
}

public function addShopCategoryTabCollection(shopCategoryTab $shopCategoryTab): self
{
    if (!$this->shopCategoryTabCollection->contains($shopCategoryTab)) {
        $this->shopCategoryTabCollection->add($shopCategoryTab);
        $shopCategoryTab->setShopCategory($this);
    }

    return $this;
}

public function removeShopCategoryTabCollection(shopCategoryTab $shopCategoryTab): self
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
