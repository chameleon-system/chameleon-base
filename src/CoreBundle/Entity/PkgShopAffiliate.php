<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopAffiliate {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Name */
    public readonly string $name, 
    /** URL parameter used to transfer the tracking code */
    public readonly string $urlParameterName, 
    /** Seconds, for which the code is still valid with inactive session */
    public readonly string $numberOfSecondsValid, 
    /** Class */
    public readonly string $class, 
    /** Class subtype (path relative to ./classes) */
    public readonly string $classSubtype, 
    /** Class type */
    public readonly string $classType, 
    /** Code to be integrated on order success page */
    public readonly string $orderSuccessCode, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliateParameter[] Parameter */
    public readonly array $pkgShopAffiliateParameter  ) {}
}