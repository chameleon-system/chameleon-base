<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandlerGroup {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Overwrite Tdb with this class */
    public readonly string $classname, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroupConfig[] Configuration */
    public readonly array $shopPaymentHandlerGroupConfig, 
    /** IPN Identifier */
    public readonly string $ipnGroupIdentifier, 
    /** Character encoding of data transmitted by the provider */
    public readonly string $ipnPayloadCharacterCharset, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus[] IPN status codes */
    public readonly array $pkgShopPaymentIpnStatus, 
    /** Name */
    public readonly string $name, 
    /** System name */
    public readonly string $systemName, 
    /** Description */
    public readonly string $description, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler[] Payment handler */
    public readonly array $shopPaymentHandler, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod[] Payment methods */
    public readonly array $shopPaymentMethod, 
    /** IPN may come from the following IP */
    public readonly string $ipnAllowedIps, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger[] Redirections */
    public readonly array $pkgShopPaymentIpnTrigger, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage[] IPN messages */
    public readonly array $pkgShopPaymentIpnMessage, 
    /** Environment */
    public readonly string $environment  ) {}
}