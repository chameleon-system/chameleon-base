<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsClassManagerExtension {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCmsClassManager $pkgCmsClassManagerId, 
    /** Class */
    public readonly string $class, 
    /** Path relative to library/classes */
    public readonly string $classSubtype, 
    /** Class type */
    public readonly string $classType, 
    /** Sorting */
    public readonly int $position  ) {}
}