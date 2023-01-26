<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsWizardConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title / headline */
    public readonly string $name, 
    /** Specifies whether the list of steps is in a package */
    public readonly bool $listIsPackage  ) {}
}