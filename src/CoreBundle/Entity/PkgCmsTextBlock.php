<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsTextBlock {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Available in selected portals */
    public readonly array $cmsPortalMlt, 
    /** System name */
    public readonly string $systemname, 
    /** Name / title */
    public readonly string $name, 
    /** Text */
    public readonly string $content  ) {}
}