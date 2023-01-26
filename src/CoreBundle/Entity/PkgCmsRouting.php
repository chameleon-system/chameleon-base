<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsRouting {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** System name */
    public readonly string $name, 
    /** Brief description */
    public readonly string $shortDescription, 
    /** Type of resource */
    public readonly string $type, 
    /** Resource */
    public readonly string $resource, 
    /** Position */
    public readonly int $position, 
    /** System page */
    public readonly string $systemPageName, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Restrict to the following portals */
    public readonly array $cmsPortalMlt, 
    /** Active */
    public readonly bool $active  ) {}
}