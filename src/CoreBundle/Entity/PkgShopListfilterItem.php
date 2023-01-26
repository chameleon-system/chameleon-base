<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopListfilterItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to list filter configuration */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter $pkgShopListfilterId, 
    /** Filter type */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItemType $pkgShopListfilterItemType, 
    /** Name */
    public readonly string $name, 
    /** System name */
    public readonly string $systemname, 
    /** Belonging product attribute */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopAttribute $shopAttribute, 
    /** Multiple selections */
    public readonly bool $allowMultiSelection, 
    /** Show all when opening the page? */
    public readonly bool $showAllOnPageLoad, 
    /** Window size */
    public readonly string $previewSize, 
    /** Show scrollbars instead of &quot;show all&quot; button? */
    public readonly bool $showScrollbars, 
    /** Lowest value */
    public readonly string $minValue, 
    /** Highest value */
    public readonly string $maxValue, 
    /** MySQL field name */
    public readonly string $mysqlFieldName, 
    /** View */
    public readonly string $view, 
    /** View class type */
    public readonly string $viewClassType, 
    /** Sorting */
    public readonly int $position, 
    /** System name of the variant type */
    public readonly string $variantIdentifier  ) {}
}