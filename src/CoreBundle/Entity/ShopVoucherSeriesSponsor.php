<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVoucherSeriesSponsor {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Logo */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId  ) {}
}