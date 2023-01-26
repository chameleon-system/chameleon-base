<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsThemeBlockLayout {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to theme block */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock $pkgCmsThemeBlockId, 
    /** Descriptive name */
    public readonly string $name, 
    /** Layout file (path) */
    public readonly string $layoutFile, 
    /** Path to own LESS/CSS */
    public readonly string $lessFile, 
    /** Preview image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId  ) {}
}