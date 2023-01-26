<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopListfilterModuleConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /**  */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter $pkgShopListfilterId, 
    /** Filter parameters */
    public readonly string $filterParameter  ) {}
}