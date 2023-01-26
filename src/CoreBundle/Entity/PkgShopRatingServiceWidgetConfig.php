<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingServiceWidgetConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Rating service */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService $pkgShopRatingServiceId  ) {}
}