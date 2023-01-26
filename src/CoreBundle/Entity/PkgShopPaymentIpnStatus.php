<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentIpnStatus {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to the configuration of */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup $shopPaymentHandlerGroupId, 
    /** Name */
    public readonly string $name, 
    /** Code (of the provider) */
    public readonly string $code, 
    /** Description */
    public readonly string $description  ) {}
}