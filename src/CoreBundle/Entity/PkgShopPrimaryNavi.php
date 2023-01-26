<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPrimaryNavi {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Name */
    public readonly string $name, 
    /** Active */
    public readonly bool $active, 
    /** Position */
    public readonly int $position, 
    /** Select navigation */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree|\ChameleonSystem\CoreBundle\Entity\ShopCategory $target, 
    /** Replace submenu with shop main categories */
    public readonly bool $showRootCategoryTree, 
    /** Individual CSS class */
    public readonly string $cssClass  ) {}
}