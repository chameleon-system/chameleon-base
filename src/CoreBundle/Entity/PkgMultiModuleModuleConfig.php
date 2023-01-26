<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgMultiModuleModuleConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Multimodule set */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet $pkgMultiModuleSetId  ) {}
}