<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleCustomlistConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title */
    public readonly string $name, 
    /** Introduction text */
    public readonly string $intro, 
    /** Items per page */
    public readonly string $recordsPerPage, 
    /** Grouping field */
    public readonly string $groupField, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfigSortfields[] Sorting */
    public readonly array $orderinfo  ) {}
}