<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandlerGroupConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup $shopPaymentHandlerGroupId, 
    /** Name */
    public readonly string $name, 
    /** Type */
    public readonly string $type, 
    /** Portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Value */
    public readonly string $value, 
    /** Description */
    public readonly string $description  ) {}
}