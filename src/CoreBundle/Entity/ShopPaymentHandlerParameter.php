<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandlerParameter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to payment handler */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler $shopPaymentHandlerId, 
    /** Display name */
    public readonly string $name, 
    /** Type */
    public readonly string $type, 
    /** System name */
    public readonly string $systemname, 
    /** Description */
    public readonly string $description, 
    /** Value */
    public readonly string $value, 
    /** Applies to this portal only */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId  ) {}
}