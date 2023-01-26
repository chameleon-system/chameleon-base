<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleListCat {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title */
    public readonly string $name, 
    /** Sorting order */
    public readonly int $sortOrder  ) {}
}