<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsPortalNavigation {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Navigation title */
    public readonly string $name, 
    /** Start node in navigation tree */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $treeNode  ) {}
}