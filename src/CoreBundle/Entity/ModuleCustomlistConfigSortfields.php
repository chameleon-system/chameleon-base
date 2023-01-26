<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleCustomlistConfigSortfields {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to list */
    public readonly \ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfig $moduleCustomlistConfigId, 
    /** Field name */
    public readonly string $name, 
    /** Direction */
    public readonly string $direction, 
    /** Position */
    public readonly int $position  ) {}
}