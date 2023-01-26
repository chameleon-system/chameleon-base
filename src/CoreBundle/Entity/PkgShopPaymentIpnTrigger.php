<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentIpnTrigger {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to payment provider */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup $shopPaymentHandlerGroupId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessageTrigger[]  */
    public readonly array $pkgShopPaymentIpnMessageTrigger, 
    /** Active */
    public readonly bool $active, 
    /** Target URL */
    public readonly string $targetUrl, 
    /** Timeout */
    public readonly string $timeoutSeconds, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus[] Status codes to be forwarded */
    public readonly array $pkgShopPaymentIpnStatusMlt  ) {}
}