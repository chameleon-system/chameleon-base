<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMenuItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Target */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf|\ChameleonSystem\CoreBundle\Entity\CmsModule|\ChameleonSystem\CoreBundle\Entity\CmsMenuCustomItem $target, 
    /** Icon font CSS class */
    public readonly string $iconFontCssClass, 
    /** Position */
    public readonly int $position, 
    /** CMS main menu category */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMenuCategory $cmsMenuCategoryId  ) {}
}