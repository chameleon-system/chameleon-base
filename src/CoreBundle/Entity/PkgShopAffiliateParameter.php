<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopAffiliateParameter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to affiliate program */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate $pkgShopAffiliateId, 
    /** Name */
    public readonly string $name, 
    /** Value */
    public readonly string $value  ) {}
}