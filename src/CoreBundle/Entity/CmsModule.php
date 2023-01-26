<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsModule {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Icon */
    public readonly string $iconList, 
    /** Description */
    public readonly string $name, 
    /** CMS abbreviation */
    public readonly string $uniquecmsname, 
    /** Show in category window */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsContentBox $cmsContentBoxId, 
    /** Module belongs to group */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUsergroup $cmsUsergroupId, 
    /** Module page configuration */
    public readonly string $module, 
    /** URL parameter */
    public readonly string $parameter, 
    /** Module type */
    public readonly string $moduleLocation, 
    /** Open as popup */
    public readonly bool $showAsPopup, 
    /** Popup window width */
    public readonly string $width, 
    /** Popup window height */
    public readonly string $height, 
    /** Active */
    public readonly bool $active, 
    /** Icon Font CSS class */
    public readonly string $iconFontCssClass  ) {}
}