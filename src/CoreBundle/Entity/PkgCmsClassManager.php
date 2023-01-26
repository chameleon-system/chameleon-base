<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsClassManager {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Class names of the virtual entry class */
    public readonly string $nameOfEntryPoint, 
    /** Terminate inheritance with this class */
    public readonly string $exitClass, 
    /** End item class: path */
    public readonly string $exitClassSubtype, 
    /** End item class: type */
    public readonly string $exitClassType, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsClassManagerExtension[] Classes administered by the inheritance manager */
    public readonly array $pkgCmsClassManagerExtension  ) {}
}