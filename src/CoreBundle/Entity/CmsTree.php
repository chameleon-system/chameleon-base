<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTree {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Is subnode of */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $parentId, 
    /** Nested set: left */
    public readonly string $lft, 
    /** Name */
    public readonly string $name, 
    /** Nested set: right */
    public readonly string $rgt, 
    /** URL name */
    public readonly string $urlname, 
    /** Hide */
    public readonly bool $hidden, 
    /** Show restricted page in navigation */
    public readonly bool $showExtranetPage, 
    /** Position */
    public readonly string $entrySort, 
    /** External link */
    public readonly string $link, 
    /** Open link in new window */
    public readonly bool $linkTarget, 
    /** Pages / layouts */
    public readonly string $cmsTplPagePrimaryLink, 
    /** Icon for navigation */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $naviIconCmsMediaId, 
    /** Navigation path cache */
    public readonly string $pathcache, 
    /** Connect module to navigation */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** SEO: no follow */
    public readonly bool $seoNofollow, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPage[] SEO: no follow - page exclusion list */
    public readonly array $cmsTplPageMlt, 
    /** Hotkeys */
    public readonly string $htmlAccessKey, 
    /** CSS classes */
    public readonly string $cssClasses, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTreeNode[] Connected pages */
    public readonly array $cmsTreeNode  ) {}
}