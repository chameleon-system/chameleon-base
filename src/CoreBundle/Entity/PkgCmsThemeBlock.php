<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsThemeBlock {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Descriptive name */
    public readonly string $name, 
    /** System name */
    public readonly string $systemName, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot[] Spots */
    public readonly array $cmsMasterPagedefSpot, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout[] Layouts */
    public readonly array $pkgCmsThemeBlockLayout, 
    /** Default layout */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout $pkgCmsThemeBlockLayoutId, 
    /** Preview image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId  ) {}
}