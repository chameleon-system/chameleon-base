<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgExternalTracker {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Active */
    public readonly bool $active, 
    /** User / site code */
    public readonly string $identifier, 
    /** User / site code in DEMO MODE */
    public readonly string $testIdentifier, 
    /** Class */
    public readonly string $class, 
    /** Class subtype (path) */
    public readonly string $classSubtype, 
    /** Class type */
    public readonly string $classType, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Portal selection */
    public readonly array $cmsPortalMlt  ) {}
}