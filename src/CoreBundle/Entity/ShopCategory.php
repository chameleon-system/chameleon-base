<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopCategory {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory|null - Subcategory of */
private \ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory = null,
/** @var null|string - Subcategory of */
private ?string $shopCategoryId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Icon for navigation */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $naviIconCmsMedia = null,
/** @var null|string - Icon for navigation */
private ?string $naviIconCmsMediaId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVat|null - VAT group */
private \ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat = null,
/** @var null|string - VAT group */
private ?string $shopVatId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Category image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $image = null,
/** @var null|string - Category image */
private ?string $imageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null - List filter for the category */
private \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilter = null,
/** @var null|string - List filter for the category */
private ?string $pkgShopListfilterId = null
, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Template for the details page */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $detailPageCmsTreeId = null, 
    // TCMSFieldText
/** @var string - URL path */
private string $urlPath = '', 
    // TCMSFieldVarchar
/** @var string - Category name */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = true, 
    // TCMSFieldBoolean
/** @var bool - Is the tree active up to this category? */
private bool $treeActive = true, 
    // TCMSFieldVarchar
/** @var string - Additional product name */
private string $nameProduct = '', 
    // TCMSFieldVarchar
/** @var string - SEO pattern */
private string $seoPattern = '', 
    // TCMSFieldColorpicker
/** @var string - Color code */
private string $colorcode = '', 
    // TCMSFieldBoolean
/** @var bool - Highlight category */
private bool $categoryHightlight = false, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Subcategories */
private \Doctrine\Common\Collections\Collection $shopCategoryCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldWYSIWYG
/** @var string - Short description of the category */
private string $descriptionShort = '', 
    // TCMSFieldWYSIWYG
/** @var string - Detailed description of the category */
private string $description = '', 
    // TCMSFieldVarchar
/** @var string - Meta keywords */
private string $metaKeywords = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategoryTab[] - Category */
private \Doctrine\Common\Collections\Collection $shopCategoryTabCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Meta description */
private string $metaDescription = ''  ) {}

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
public function getShopCategory(): \ChameleonSystem\CoreBundle\Entity\ShopCategory|null
{
    return $this->shopCategory;
}
public function setShopCategory(\ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory): self
{
    $this->shopCategory = $shopCategory;
    $this->shopCategoryId = $shopCategory?->getId();

    return $this;
}
public function getShopCategoryId(): ?string
{
    return $this->shopCategoryId;
}
public function setShopCategoryId(?string $shopCategoryId): self
{
    $this->shopCategoryId = $shopCategoryId;
    // todo - load new id
    //$this->shopCategoryId = $?->getId();

    return $this;
}



  
    // TCMSFieldTreeNode
public function getDetailPageCmsTreeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->detailPageCmsTreeId;
}
public function setDetailPageCmsTreeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $detailPageCmsTreeId): self
{
    $this->detailPageCmsTreeId = $detailPageCmsTreeId;

    return $this;
}


  
    // TCMSFieldLookup
public function getNaviIconCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->naviIconCmsMedia;
}
public function setNaviIconCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $naviIconCmsMedia): self
{
    $this->naviIconCmsMedia = $naviIconCmsMedia;
    $this->naviIconCmsMediaId = $naviIconCmsMedia?->getId();

    return $this;
}
public function getNaviIconCmsMediaId(): ?string
{
    return $this->naviIconCmsMediaId;
}
public function setNaviIconCmsMediaId(?string $naviIconCmsMediaId): self
{
    $this->naviIconCmsMediaId = $naviIconCmsMediaId;
    // todo - load new id
    //$this->naviIconCmsMediaId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getUrlPath(): string
{
    return $this->urlPath;
}
public function setUrlPath(string $urlPath): self
{
    $this->urlPath = $urlPath;

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


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldBoolean
public function isTreeActive(): bool
{
    return $this->treeActive;
}
public function setTreeActive(bool $treeActive): self
{
    $this->treeActive = $treeActive;

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
public function getShopVat(): \ChameleonSystem\CoreBundle\Entity\ShopVat|null
{
    return $this->shopVat;
}
public function setShopVat(\ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat): self
{
    $this->shopVat = $shopVat;
    $this->shopVatId = $shopVat?->getId();

    return $this;
}
public function getShopVatId(): ?string
{
    return $this->shopVatId;
}
public function setShopVatId(?string $shopVatId): self
{
    $this->shopVatId = $shopVatId;
    // todo - load new id
    //$this->shopVatId = $?->getId();

    return $this;
}



  
    // TCMSFieldColorpicker
public function getColorcode(): string
{
    return $this->colorcode;
}
public function setColorcode(string $colorcode): self
{
    $this->colorcode = $colorcode;

    return $this;
}


  
    // TCMSFieldBoolean
public function isCategoryHightlight(): bool
{
    return $this->categoryHightlight;
}
public function setCategoryHightlight(bool $categoryHightlight): self
{
    $this->categoryHightlight = $categoryHightlight;

    return $this;
}


  
    // TCMSFieldLookup
public function getImage(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->image;
}
public function setImage(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $image): self
{
    $this->image = $image;
    $this->imageId = $image?->getId();

    return $this;
}
public function getImageId(): ?string
{
    return $this->imageId;
}
public function setImageId(?string $imageId): self
{
    $this->imageId = $imageId;
    // todo - load new id
    //$this->imageId = $?->getId();

    return $this;
}



  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopCategoryCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopCategoryCollection;
}
public function setShopCategoryCollection(\Doctrine\Common\Collections\Collection $shopCategoryCollection): self
{
    $this->shopCategoryCollection = $shopCategoryCollection;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescriptionShort(): string
{
    return $this->descriptionShort;
}
public function setDescriptionShort(string $descriptionShort): self
{
    $this->descriptionShort = $descriptionShort;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

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
public function getPkgShopListfilter(): \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null
{
    return $this->pkgShopListfilter;
}
public function setPkgShopListfilter(\ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilter): self
{
    $this->pkgShopListfilter = $pkgShopListfilter;
    $this->pkgShopListfilterId = $pkgShopListfilter?->getId();

    return $this;
}
public function getPkgShopListfilterId(): ?string
{
    return $this->pkgShopListfilterId;
}
public function setPkgShopListfilterId(?string $pkgShopListfilterId): self
{
    $this->pkgShopListfilterId = $pkgShopListfilterId;
    // todo - load new id
    //$this->pkgShopListfilterId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getShopCategoryTabCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopCategoryTabCollection;
}
public function setShopCategoryTabCollection(\Doctrine\Common\Collections\Collection $shopCategoryTabCollection): self
{
    $this->shopCategoryTabCollection = $shopCategoryTabCollection;

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
