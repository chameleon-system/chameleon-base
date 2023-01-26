<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopCategory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Subcategory of */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopCategory $shopCategoryId, 
    /** Template for the details page */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $detailPageCmsTreeId, 
    /** Icon for navigation */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $naviIconCmsMediaId, 
    /** URL path */
    public readonly string $urlPath, 
    /** Category name */
    public readonly string $name, 
    /** Active */
    public readonly bool $active, 
    /** Is the tree active up to this category? */
    public readonly bool $treeActive, 
    /** Additional product name */
    public readonly string $nameProduct, 
    /** SEO pattern */
    public readonly string $seoPattern, 
    /** VAT group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVat $shopVatId, 
    /** Color code */
    public readonly string $colorcode, 
    /** Highlight category */
    public readonly bool $categoryHightlight, 
    /** Category image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $image, 
    /** Position */
    public readonly int $position, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] Subcategories */
    public readonly array $shopCategory, 
    /** Short description of the category */
    public readonly string $descriptionShort, 
    /** Detailed description of the category */
    public readonly string $description, 
    /** Meta keywords */
    public readonly string $metaKeywords, 
    /** List filter for the category */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter $pkgShopListfilterId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopCategoryTab[] Category */
    public readonly array $shopCategoryTab, 
    /** Meta description */
    public readonly string $metaDescription  ) {}
}