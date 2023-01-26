<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopManufacturerModuleConf {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title / headline */
    public readonly string $name, 
    /** Icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Introduction text */
    public readonly string $intro  ) {}
}