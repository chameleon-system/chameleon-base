<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgMultiModuleSet {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name of the set */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSetItem[] Set consists of these modules */
    public readonly array $pkgMultiModuleSetItem  ) {}
}