<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMasterPagedef {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Description */
    public readonly string $description, 
    /** Layout */
    public readonly string $layout, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot[] Spots */
    public readonly array $cmsMasterPagedefSpot, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock[] Theme blocks */
    public readonly array $pkgCmsThemeBlockMlt, 
    /** Action-Plugins */
    public readonly string $actionPluginList, 
    /** Restrict to certain portals only */
    public readonly bool $restrictToPortals, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] CMS module extension */
    public readonly array $cmsPortalMlt, 
    /** WYSIWYG CSS URL */
    public readonly string $wysiwygCssUrl, 
    /**  */
    public readonly int $position  ) {}
}