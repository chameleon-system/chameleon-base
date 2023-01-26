<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentIpnMessage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessageTrigger[] Forwarding logs */
    public readonly array $pkgShopPaymentIpnMessageTrigger, 
    /** Activated via this portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Belongs to order (ID) */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Payment provider */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup $shopPaymentHandlerGroupId, 
    /** Date */
    public readonly \DateTime $datecreated, 
    /** Status */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus $pkgShopPaymentIpnStatusId, 
    /** Processed successfully */
    public readonly bool $success, 
    /** Processed message */
    public readonly bool $completed, 
    /** Type of error */
    public readonly string $errorType, 
    /** IP */
    public readonly string $ip, 
    /** Request URL */
    public readonly string $requestUrl, 
    /** Payload */
    public readonly string $payload, 
    /** Error details */
    public readonly string $errors  ) {}
}