<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsPortalSystemPage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Name */
    public readonly string $name, 
    /** Page */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $cmsTreeId, 
    /** System name */
    public readonly string $nameInternal  ) {}
}